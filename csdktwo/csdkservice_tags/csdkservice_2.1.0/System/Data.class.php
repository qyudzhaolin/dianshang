<?php
/**
 * | 数据库操作辅助类
 */
namespace System;

class DBHelperApi{
    /**
     * 默认数据库配置标识/读操作标识
     */
    const DEFAULT_DBCONFIG_FLAG_READ = '__MASTER_READ__';
    /**
     * 默认数据库配置标识/写操作标识
     */
    const DEFAULT_DBCONFIG_FLAG_WRITE = '__MASTER_WRITE__';
    
    /**
     * 数据库访问对象池
     * @var \System\DbAccessObject
     */
    private static $daoPool = array();
    
    /**
     * 默认数据库配置, 未初始化时为null
     * @var array
     */
    private static $defaultDbConfig = null;
    
    /**
     * 第三方应用数据库, 未初始化时为null
     * @var array
     */
    private static $otherDbConfig = null;
    
    /**
     * 当前是否处于主库Only模式(不读写分离/不负载均衡)
     * @var bool
     */
    private static $inMasterOnlyMode = false;
    
    /**
     * 当前是否正处在事务中
     * @var bool
     */
    private static $inTransaction = false;
    
    /**
     * 上一个活动的DAO对象标识
     * @var string
     */
    private $lastActiveDAO = null;

    public function __construct( ){
        // 如果尚未进行过配置初始化, 则开始进行初始化
        if( self::$defaultDbConfig === null && self::$otherDbConfig === null ) {
            $this->initDBConfig( ); // 初始化配置项
            
            self::$otherDbConfig = self::$otherDbConfig === null ? array() : self::$otherDbConfig; // 初始化为非null值, 以防再次重复进行初始化
            self::$defaultDbConfig = self::$defaultDbConfig === null ? array() : self::$defaultDbConfig; // 初始化为非null值, 以防再次重复进行初始化
        }
    }

    /**
     * 严格判断一个变量是否为数字或数字格式字符串(有前导零的认为不是数字)
     * @param mixed $var
     *        要判断的变量, 如: '123.5', '021'
     * @return boolean 判断结果
     */
    private function isNumeric( $var ){
        if( is_numeric( $var ) === false || ( strpos( $var, '0' ) === 0 && $var != 0 ) )
            return false;
        return true;
    }

    
    /**
     * 初始化数据库的配置数据
     * @throws \Exception
     */
    private function initDBConfig( ){
        // 初始化主域名以外的第三方数据库系统配置
        // if(defined('DBHELPER_CONFIG_PATH') && file_exists(DBHELPER_CONFIG_PATH))
        // $dbConfigPath = DBHELPER_CONFIG_PATH;
        // else
        // $dbConfigPath = dirname(__FILE__);
        // $dbConfigPath = $dbConfigPath.DIRECTORY_SEPARATOR.'DBHelper.Config.php';
        // $configArr = require $dbConfigPath ;
        $configArr = C( 'DBHELPER' );
        
        // 如果配置了第三方的其他数据库, 则进行初始化, 并unset掉
        if( isset( $configArr['OTHER'] ) ) {
            self::$otherDbConfig = $configArr['OTHER'];
            unset( $configArr['OTHER'] );
        }
        // 如果没有配置主数据库的host/port/user/pwd/name, 则报错退出
        if( isset( $configArr['MASTER'] ) == false || isset( $configArr['MASTER']['HOST'] ) == false || isset( $configArr['MASTER']['PORT'] ) == false || isset( $configArr['MASTER']['USER'] ) == false || isset( $configArr['MASTER']['PWD'] ) == false || isset( $configArr['MASTER']['NAME'] ) == false )
            throw new \Exception( '数据库配置错误！缺少最基本的主数据库连接参数' );
            
        // 如果未设置MASTER_BEAR_READ参数, 则重置为默认false
        $configArr['MASTER_BEAR_READ'] = isset( $configArr['MASTER_BEAR_READ'] ) ? $configArr['MASTER_BEAR_READ'] : false;
        // 如果未设置DB_TABLE_PREFIX参数, 则重置为默认''
        $configArr['DB_TABLE_PREFIX'] = isset( $configArr['DB_TABLE_PREFIX'] ) ? $configArr['DB_TABLE_PREFIX'] : '';
        
        self::$defaultDbConfig = $configArr; // 存储配置项到静态变量内
    }

    /**
     * 添加一个其他数据库配置
     * @param string $configFlag
     *        在后续的查询等操作时候会用到
     * @param array $dbConfig
     *        如: array('HOST'=>'127.0.0.1','PORT'=>3306,'USER'=>'root','PWD'=>'','NAME'=>'test','CHARSET'=>'utf8');
     * @throws \Exception
     */
    public function addOtherDbConfig( $configFlag, $dbConfig ){
        if( is_array( $dbConfig ) && isset( $dbConfig['HOST'] ) && isset( $dbConfig['PORT'] ) && isset( $dbConfig['USER'] ) && isset( $dbConfig['PWD'] ) && isset( $dbConfig['NAME'] ) && isset( $dbConfig['CHARSET'] ) )
            self::$otherDbConfig[$configFlag] = $dbConfig;
        else
            throw new \Exception( "要添加的连接配置参数错误! 请仔细检查" );
    }

    /**
     * 移除已设置的数据库连接配置参数
     * @param string $configFlag
     *        要移除的配置名称, 如果不指定, 则默认移除全部
     */
    public function removeOtherDbConfig( $configFlag = null ){
        if( $configFlag === null )
            self::$otherDbConfig = array(); // 清除所有配置
        else
            unset( self::$otherDbConfig[$configFlag] ); // 清除指定的配置
    }

    /**
     * 初始化默认数据库的读写操作对象
     */
    private function initDefaultDAO( ){
        $dbWriteConf = self::$defaultDbConfig['MASTER'];
        
        /******************START 监控代码********************/
        $monitor = MONITOR_MYSQL_THE ? new \System\Monitor\MonitorMysql() : NULL;
        /******************END 监控代码**********************/
        
        self::$daoPool[self::DEFAULT_DBCONFIG_FLAG_WRITE] = new DbAccessObject( $dbWriteConf, $monitor );
        
        // 计算可以承受读的服务器数量
        $canReadDBCount = isset( self::$defaultDbConfig['SLAVE'] ) ? sizeof( self::$defaultDbConfig['SLAVE'] ) : 0;
        // 如果配置了主服务器承担读任务, 或者没有配置任何从服务器, 则 对可承受读操作的服务器数量+1
        if( self::$defaultDbConfig['MASTER_BEAR_READ'] == true || $canReadDBCount == 0 )
            $canReadDBCount++;
        
        $dbReadConf = null;
        // 随机选择本次要承担读操作的数据库配置索引
        $currentUseDBIndex = mt_rand( 0, $canReadDBCount - 1 );
        // 如果主库负载读压力 且 本次使用的索引为, 总配置数-1, 则使用主服务器
        if( self::$defaultDbConfig['MASTER_BEAR_READ'] == true  && $currentUseDBIndex == $canReadDBCount - 1 )
            $dbReadConf = self::$defaultDbConfig['MASTER'];
        else
            $dbReadConf = self::$defaultDbConfig['SLAVE'][$currentUseDBIndex];
        
        self::$daoPool[self::DEFAULT_DBCONFIG_FLAG_READ] = new DbAccessObject( $dbReadConf, $monitor );
    }

    
    /**
     * 获取用于指定数据库的读操作数据访问对象
     * @param string $dbConfigFlag
     *        则使用主数据库的配置
     * @return \System\DbAccessObject
     */
    private function getReadDAO( $dbConfigFlag = null ){
        //如果当前操作未指定数据库链接标识, 
        //且  当前处于MasterOnly模式 或者 事务环境中
        //则  将数据库链接标识 指定为 主库
        if( $dbConfigFlag===null && ( self::$inMasterOnlyMode==true || self::$inTransaction==true) )
            $dbConfigFlag = self::DEFAULT_DBCONFIG_FLAG_WRITE;

        elseif( $dbConfigFlag === null )
            $dbConfigFlag = self::DEFAULT_DBCONFIG_FLAG_READ;
        
        return $this->getDAO( $dbConfigFlag );
    }

    /**
     * 获取用于指定数据库的写操作数据访问对象
     * @param string $dbConfigFlag
     *        则使用主数据库的配置
     * @return \System\DbAccessObject
     */
    private function getWriteDAO( $dbConfigFlag = null ){
        if( $dbConfigFlag === null )
            $dbConfigFlag = self::DEFAULT_DBCONFIG_FLAG_WRITE;
        
        return $this->getDAO( $dbConfigFlag );
    }

    /**
     * 获取指定数据库配置的数据库访问对象
     * @param string $dbConfigFlag        
     * @throws \Exception
     * @return \System\DbAccessObject
     */
    private function getDAO( $dbConfigFlag ){
        $this->lastActiveDAO = $dbConfigFlag;
        
        // if the dao exist, direct return
        if( isset( self::$daoPool[$dbConfigFlag] ) )
            return self::$daoPool[$dbConfigFlag];
        
        if( $dbConfigFlag == self::DEFAULT_DBCONFIG_FLAG_READ || $dbConfigFlag == self::DEFAULT_DBCONFIG_FLAG_WRITE ) {
            $this->initDefaultDAO( );
            return self::$daoPool[$dbConfigFlag];
        }
        
        if( isset( self::$otherDbConfig[$dbConfigFlag] ) == false )
            throw new \Exception( "指定标识[{$dbConfigFlag}]的数据库连接配置不存在" );
            
        // init other db config DAO
        MONITOR_MYSQL_THE && $monitor = new \System\Monitor\MonitorMysql();
        self::$daoPool[$dbConfigFlag] = new DbAccessObject( self::$otherDbConfig[$dbConfigFlag], $monitor );
        
        return self::$daoPool[$dbConfigFlag];
    }

    /**
     * 获取主数据库的表前缀
     * @return string 表前缀
     */
    protected function getMainDbTablePrefix( ){
        return self::$defaultDbConfig['DB_TABLE_PREFIX'];
    }

    /**
     * 获取当前是否处于MasterOnly模式
     * @return boolean
     */
    public function isInMasterOnlyMode(){
        return self::$inMasterOnlyMode;
    }
    
    /**
     * 开启MasterOnly模式(不读写分离/不负载均衡)
     */
    public function startMasterOnlyMode(){
    	self::$inMasterOnlyMode = true;
    }
    
    /**
     * 关闭MasterOnly模式(根据配置进行读写分离和负载均衡)
     */
    public function stopMasterOnlyMode(){
    	self::$inMasterOnlyMode = false;
    }
    
    /**
     * 获取当前是否处于事务环境
     * @return boolean
     */
    public function isInTransaction(){
    	return self::$inTransaction;
    }
    
    /**
     * 开始数据库事务
     * @param string $dbConfigFlag
     *        默认为主数据库, 如需切换至第三方, 需要先添加, 然后传入对应的配置名称
     * @return void
     */
    public function startTransaction( $dbConfigFlag = null ){
        $this->getWriteDAO( $dbConfigFlag )->startTrans( );
        self::$inTransaction = true;
    }

    /**
     * 提交数据库事务
     * @param string $dbConfigFlag
     *        默认为主数据库, 如需切换至第三方, 需要先添加, 然后传入对应的配置名称
     * @return void
     */
    public function commitTransaction( $dbConfigFlag = null ){
        $result = $this->getWriteDAO( $dbConfigFlag )->commit( );
        self::$inTransaction = false;
        return $result;
    }

    /**
     * 回滚数据库事务
     * @param string $dbConfigFlag
     *        默认为主数据库, 如需切换至第三方, 需要先添加, 然后传入对应的配置名称
     * @return void
     */
    public function rollbackTransaction( $dbConfigFlag = null ){
        $result = $this->getWriteDAO( $dbConfigFlag )->rollback( );
        self::$inTransaction = false;
        return $result;
    }

    /**
     * 将预格式化的sql和参数列表进行格式化为标准sql
     * @param string $sql        
     * @param array $args        
     * @return string
     */
    private function prepareSQLStatement( $sql, $args ){
        $keys = array();
        $values = array();
        foreach( $args as $k=>$v ) {
            $keys[] = ":{$k}";
            $values[] = $this->escapeQueryParam( $v );
        }
        return str_replace( $keys, $values, $sql );
    }

    /**
     * 在指定或默认的数据库内执行一条查询sql
     * @param string $sql
     *        如: select * from user where id=:id
     * @param string $args
     *        如: array('id'=>3)
     * @param string $dbConfigFlag
     *        默认为主数据库, 如需切换至第三方, 需要先添加, 然后传入对应的配置名称
     * @return mixed | null
     */
    public function executeQuery( $sql, $args = null, $dbConfigFlag = null ){
        return $this->getReadDAO( $dbConfigFlag )->query( $sql, $args );
    }

    /**
     * 在指定或默认的数据库内执行一条非查询类SQL, 如DELETE/UPDATE/INSERT
     * @param string $sql
     *        如: select * from user where id=:id
     * @param string $args
     *        如: array('id'=>3)
     * @param string $dbConfigFlag
     *        默认为主数据库, 如需切换至第三方, 需要先添加, 然后传入对应的配置名称
     * @return false | integer
     */
    public function executeNonQuery( $sql, $args = null, $dbConfigFlag = null ){
        // 记录非正常执行语句
        if ( preg_match("/^(\s*)select/i", $sql) ) {
            L("####\nexecute on write server: ". $sql ."\n". getExceptionTraceAsString(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)) .'####', 7, 'ReadWrite.txt');
        }
        $result = $this->getWriteDAO( $dbConfigFlag )->execute( $sql, $args );
        return $result;
    }

    /**
     * 执行单表数据创建
     * @param string $tableName        
     * @param array $data
     *        如: array('userName'=>'tomhans', 'password'=>'css7876ew76f86sa', 'cretetime'=>137545445);
     * @param string $dbConfigFlag
     *        默认为主数据库, 如需切换至第三方, 需要先添加, 然后传入对应的配置名称
     * @return integer | false
     * @deprecated 
     */
    public function executeSingleTableCreate( $tableName, $data, $dbConfigFlag = null ){
        if( is_array( $data ) == false || sizeof( $data ) == 0 )
            throw new \Exception( '要保存的字段数据不能为空! 请检查' );
        
        $fields = implode( ',', array_keys( $data ) );
        $values = array();
        foreach( $data as $v )
            $values[] = $this->escapeQueryParam( $v );
        $values = implode( ',', $values );
        
        $sql = "INSERT INTO {$tableName}({$fields}) VALUES({$values}) ";
        return $this->executeNonQuery( $sql, null, $dbConfigFlag );
    }

    
    /**
     * 执行单表数据删除
     * @param string $tableName        
     * @param array $filters        
     * @param mixed $orderBy
     *        数组或者字符串, 默认: 不进行排序. 例如: 'id desc', array('name', 'score'=>'desc', 'age'=>'asc')
     * @param integer $limit
     *        默认为null. 不限制数量的话可以输入null
     * @param string $dbConfigFlag
     *        默认为主数据库, 如需切换至第三方, 需要先添加, 然后传入对应的配置名称
     * @return integer | false
     * @deprecated
     */
    public function executeSingleTableDelete( $tableName, $filters, $orderBy = null, $limit = null, $dbConfigFlag = null ){
        $sql = "DELETE FROM {$tableName} WHERE ";
        $sql .= $this->buildWhereCondition( $filters );
        $sql .= $orderBy === null ? '' : $this->parseOrderByParams( $orderBy ); // 处理排序语句
        $sql .= $limit === null ? '' : ' LIMIT ' . intval( $limit );
        
        return $this->executeNonQuery( $sql, null, $dbConfigFlag );
    }

    
    /**
     * 执行单表数据更新
     * @param string $tableName        
     * @param array $updates        
     * @param array $filters        
     * @param integer $limit
     *        默认为null. 不限制数量的话可以输入null
     * @param string $dbConfigFlag
     *        默认为主数据库, 如需切换至第三方, 需要先添加, 然后传入对应的配置名称
     * @return integer | false
     * @deprecated
     */
    public function executeSingleTableUpdate( $tableName, $updates, $filters, $limit = null, $dbConfigFlag = null ){
        $setSql = '';
        foreach( $updates as $k=>$v )
            $setSql .= $k . '=' . $this->escapeQueryParam( $v ) . ',';
        $setSql = rtrim( $setSql, ',' );
        
        $filterSql = $this->buildWhereCondition( $filters );
        
        $limit = $limit === null ? '' : 'LIMIT ' . intval( $limit );
        
        $sql = "UPDATE {$tableName} SET {$setSql} WHERE {$filterSql} {$limit}";
        return $this->executeNonQuery( $sql, null, $dbConfigFlag );
    }

    /**
     * 执行单表数据查询
     * @param string $tableName        
     * @param mixed $fields
     *        字符串或数组. 默认: *
     * @param array $filters
     *        默认: 不进行数据过滤
     * @param mixed $orderBy
     *        数组或者字符串, 默认: 不进行排序. 例如: 'id desc', array('name', 'score'=>'desc', 'age'=>'asc')
     * @param integer $limit
     *        默认为null不限制结果集数量, 需要不限制的时候可以输入null
     * @param integer $offset
     *        默认为0. 如果$limit===null, 则此参数不生效
     * @param string $dbConfigFlag
     *        默认为主数据库, 如需切换至第三方, 需要先添加, 然后传入对应的配置名称
     * @deprecated
     */
    public function executeSingleTableQuery( $tableName, $fields = '*', $filters = null, $orderBy = null, $limit = null, $offset = 0, $dbConfigFlag = null ){
        $fields = $this->parseQueryFieldsParam( $fields );
        $sql = "SELECT {$fields} FROM {$tableName} WHERE ";
        // $sql .= $filters===null ? '' : $this->buildWhereCondition($filters); //处理查询语句
        $sql .= $this->buildWhereCondition( $filters ); // 处理查询语句
        $sql .= $orderBy === null ? '' : $this->parseOrderByParams( $orderBy ); // 处理排序语句
        $sql .= $limit === null ? '' : " LIMIT {$limit} OFFSET {$offset}";
        return $this->executeQuery( $sql, null, $dbConfigFlag );
    }

    /**
     * 构建SQL语句中Where部分条件
     * @param array $args
     *        key为列名, value为条件值, 重名key使用尾部空格扩展进行区分. 支持表达式查询参考参数如:<pre>
     *        $where = array(
     *        'schoolId'=>9,
     *        'year'=>2014,
     *        'month'=>10,
     *        'type'=>'student',
     *        'class_level'=>array('in', '4,5,6'),
     *        'age'=>array('between', '12,16'),
     *        'or',
     *        'age '=>array('elt', 28),
     *        'age '=>array('gt ', 35),
     *        'age '=>array('not in', '3,6,9'),
     *        'and',
     *        'name'=>array('exp', "LIKE '孙%' "),
     *        );</pre>
     * @throws \Exception
     * @return string
     * @see 用法参见ThinkPHP3.2.2模型/查询语言/表达式查询部分介绍
     */
    protected function buildWhereCondition( $args ){
        if( $args === null || ( is_array( $args ) && sizeof( $args ) == 0 ) ) {
            return ' 1=1 ';
        }else if( is_array( $args ) == false )
            throw new \Exception( '仅支持通过Array类型参数进行SQL构建! 请检查' );
        
        $sql = '';
        
        $isNeedLogicOper = false;
        foreach( $args as $k=>$v ) {
            // 逻辑符号处理
            if( $this->isNumeric( $k ) ) {
                $sql = $sql . ' ' . strtoupper( $v ) . ' ';
                $isNeedLogicOper = false;
                continue;
            }
            $logicOper = $isNeedLogicOper == true ? 'AND' : '';
            
            $k = rtrim( $k, ' ' ); // 去除列名中的占位空格符号
            $conditionStr = $k; // 拼装本次条件的列名部分
            if( is_array( $v ) ) {
                $expType = strtoupper( str_replace( ' ', '', $v[0] ) ); // 空格全部替换, 并大写
                switch( $expType ){
                    case 'EQ':
                        $conditionStr .= '=' . $this->escapeQueryParam( $v[1] );
                        break;
                    case 'NEQ':
                        $conditionStr .= '<>' . $this->escapeQueryParam( $v[1] );
                        break;
                    case 'GT':
                        $conditionStr .= '>' . $this->escapeQueryParam( $v[1] );
                        break;
                    case 'EGT':
                        $conditionStr .= '>=' . $this->escapeQueryParam( $v[1] );
                        break;
                    case 'LT':
                        $conditionStr .= '<' . $this->escapeQueryParam( $v[1] );
                        break;
                    case 'ELT':
                        $conditionStr .= '<=' . $this->escapeQueryParam( $v[1] );
                        break;
                    case 'LIKE':
                        $conditionStr .= ' LIKE ' . $this->escapeQueryParam( $v[1] );
                        break;
                    case 'BETWEEN':
                        $rangeArgs = explode( ',', $v[1] );
                        $conditionStr .= ' BETWEEN ' . $this->escapeQueryParam( $rangeArgs[0] ) . ' AND ' . $this->escapeQueryParam( $rangeArgs[1] );
                        break;
                    case 'NOTBETWEEN':
                        $rangeArgs = explode( ',', $v[1] );
                        $conditionStr .= ' NOT BETWEEN ' . $this->escapeQueryParam( $rangeArgs[0] ) . ' AND ' . $this->escapeQueryParam( $rangeArgs[1] );
                        break;
                    case 'IN':
                        $caseValueArray = is_array( $v[1] ) ? $v[1] : explode( ',', $v[1] ); // 字符串和数组参数的兼容处理
                        $caseValueStr = '';
                        foreach( $caseValueArray as $caseValue )
                            $caseValueStr .= $this->escapeQueryParam( $caseValue ) . ',';
                        $caseValueStr = rtrim( $caseValueStr, ',' );
                        $conditionStr .= ' IN( ' . $caseValueStr . ')';
                        break;
                    case 'NOTIN':
                        $caseValueArray = is_array( $v[1] ) ? $v[1] : explode( ',', $v[1] ); // 字符串和数组参数的兼容处理
                        $caseValueStr = '';
                        foreach( $caseValueArray as $caseValue )
                            $caseValueStr .= $this->escapeQueryParam( $caseValue ) . ',';
                        $caseValueStr = rtrim( $caseValueStr, ',' );
                        $conditionStr .= ' NOT IN( ' . $this->escapeQueryParam( $v[1] ) . ')';
                        break;
                    case 'EXP': // 'exp'=>'in (3,5,8) ; ex...
                        $conditionStr .= ' ' . $v[1];
                        break;
                    default:
                        throw new \Exception( '指定的条件查询表达式类型' . $expType . '不支持' );
                }
            }else if( is_string( $v ) || is_bool( $v ) || $this->isNumeric( $v ) ) {
                $conditionStr .= '=' . $this->escapeQueryParam( $v );
            }else
                throw new \Exception( '条件查询参数类型无法识别, 请仅使用数组以及简单类型' );
            
            $sql = "{$sql} {$logicOper} {$conditionStr} ";
            $isNeedLogicOper = true;
        }
        return $sql;
    }

    
    /**
     * 转换查询字段列表参数
     * @param mixed $fields
     *        用户传入的字段过滤参数, 可以是数组或者字符串
     * @throws \Exception
     * @return string 拼接好的字段列表字符串
     */
    protected function parseQueryFieldsParam( $fields ){
        if( is_string( $fields ) && strlen( $fields ) > 0 )
            return $fields;
        if( is_array( $fields ) && sizeof( $fields ) > 0 ) {
            $result = '';
            foreach( $fields as $v )
                $result = "{$result}{$v},";
            $result = rtrim( $result, ',' );
            return $result;
        }
        throw new \Exception( '字段限定参数错误! 必须为String或者Array, 且不能为空' );
    }

    /**
     * 转换查询结果排序参数
     * @param mixed $orderBy
     *        用户传入的排序参数, 可以是数组或者字符串.例如: 'id desc', array('name', 'score'=>'desc', 'age'=>'asc')
     * @throws \Exception
     * @return string 拼接好的Order By语句
     */
    protected function parseOrderByParams( $orderBy ){
        if( is_string( $orderBy ) && strlen( $orderBy ) > 0 )
            return " ORDER BY {$orderBy} ";
        if( is_array( $orderBy ) && sizeof( $orderBy ) > 0 ) {
            $result = '';
            foreach( $orderBy as $k=>$v ) {
                if( $this->isNumeric( $k ) ) // 数字索引, 则直接默认升序排列
                    $result = "{$result} {$v} ASC,";
                else // 字符串索引, 则使用k作为排序列名, v作为排序类型
                    $result = "{$result} {$k} {$v},";
            }
            $result = rtrim( $result, ',' );
            return " ORDER BY {$result} ";
        }
        throw new \Exception( '结果排序参数错误! 必须为String或者Array, 且不能为空' );
    }

    
    /**
     * 过滤处理SQL查询的参数
     * @param mixed $v
     *        要过滤的参数, 字符串或者数字
     * @return string | number	过滤处理后的结果
     */
    public function escapeQueryParam( $v ){
        return ( is_bool( $v )  ? $v : "'" . addslashes( $v ) . "'" );
     //   return ( is_bool( $v ) || $this->isNumeric( $v ) ? $v : "'" . addslashes( $v ) . "'" );
    }

    /**
     * 获取上次插入语句生成的自增ID
     * @return NULL | number
     */
    public function getLastInsertId( ){
        $id = $this->getDAO( $this->lastActiveDAO )->getLastInsID( );
        return $id == 0 ? null : $id;
    }

    
    /**
     * 返回最后执行的sql语句
     * @access public
     * @return string
     */
    public function getLastSql( ){
        return $this->getDAO( $this->lastActiveDAO )->getLastSql( );
    }

    /**
     * 获取上一次查询触发的数据库错误信息
     * @return NULL string
     */
    public function getDbErrorInfo( ){
        $error = $this->getDAO( $this->lastActiveDAO )->getDbError( );
        if( $error === '' )
            return null;
        return $error;
    }

    
    /**
     * 重置指定数组的索引为元素中的指定值, 一般用于将数据库查询获取的多条记录的数字索引改为记录主键格式
     * @param array $dataArray        
     * @param string $newIndexSource
     *        必须位于第二维, 如果是多个作为组合索引, 则传入数组即可, 如: array('id', 'type')
     * @param string $delimiter        
     * @param bool $unsetIndexKey        
     * @return array
     */
    public function resetArrayIndex( $dataArray, $newIndexSource, $delimiter = ':', $unsetIndexKey = false ){
        $resultArray = array();
        foreach( $dataArray as $k=>$v ) {
            // string格式的单key索引, 则直接赋值, 继续下一个
            if( is_string( $newIndexSource ) ) {
                $resultArray[$v[$newIndexSource]] = $v;
                if( $unsetIndexKey )
                    unset( $v[$newIndexSource] );
                continue;
            }
            // 数组格式多key组合索引处理
            $k = '';
            foreach( $newIndexSource as $index ) {
                $k .= "{$v[$index]}{$delimiter}";
                if( $unsetIndexKey )
                    unset( $v[$index] );
            }
            $k = rtrim( $k, $delimiter );
            $resultArray[$k] = $v;
        }
        return $resultArray;
    }

    /**
     * 获取一条数据
     * @param type $sql        
     * @param type $args        
     * @param type $dbConfigFlag        
     * @return type
     */
    public function getOne( $sql, $args = null, $dbConfigFlag = null ){
        $result = $this->executeQuery( $sql, $args, $dbConfigFlag );
        return $result[0];
    }
}

/**
 * 数据库访问对象
 * @author 刘靖(liujing@vacn.com.cn)
 */
use PDO;

class DbAccessObject {
    private $_conn = null;
    private $_connected = false;
    private $_config = null;
    private $_numRows = null;
    private $_lastInsertId = null;
    private $_lastSql = null;
    private $_transTimes = null;
    
    /**
     * Debug模式下，Mysql监听实例，否则为NULL.
     * @var MonitorMysql instance
     */
    protected $monitor = null;

    /**
     * Constructor
     * @param $_config
     */
    public function __construct($_config, $monitor=null) {
    	$this->_config = $_config;
    	
    	$this->monitor = $monitor;
    }
	
    /**
     * @throws \Exception
     */
    public function connect(){ //DBConnection $DBconn
        // 如果已经连接上数据库了, 则不再重复连接
        if($this->_connected == true )
            return;
        
		$this->_conn = new DBConnection('MYSQL', $this->_config);
		
        $dbCharset = isset( $this->_config['CHARSET'] ) ? $this->_config['CHARSET'] : 'utf8'; // 默认使用UTF8编码
        $this->_conn->exec("SET NAMES '{$dbCharset}' ");
        $this->_conn->exec('SET sql_mode="" '); // 设置 sql_mode
        
        $this->_connected = true;
    }
    
    public function destroy(){}

    /**
     * 执行查询语句
     * @param string $sql
     * @throws \Exception
     * @return multitype:multitype:
     * 
     * TODO:拆分执行过多逻辑
     */
    public function query($sql, $args=null){
   //    try {
            if( 0 === stripos( $sql, 'call' ) ) {// 存储过程查询支持
                $this->close( );
            }
            
            $this->connect();
            
            /******************START 监控代码********************/
            if (!empty($this->monitor)){
                $start = microtime(TRUE);
            }
            if( MONITOR_SYSLOG ) {
                $syslog_start_time = msectime();
            }
            /******************END 监控代码**********************/
            
            $stmt = $this->_conn->prepare($sql);
            
            if($args!=null && sizeof($args)>0)
                $this->_bindParams($stmt, $args);
            
            $stmt->execute();
            
            /******************START 监控代码********************/
            if (!empty($this->monitor)) {
                $end = microtime(TRUE);
                $this->monitor->log($stmt, $args, $end - $start);
            }
            if( MONITOR_SYSLOG ) {
                $syslog_end_time = msectime();
                $syslog_diff_time = $syslog_end_time - $syslog_start_time;
                if( $syslog_diff_time > MONITOR_SYSLOG_LONGTIME ) {
                    \System\Logs::info( '_debug.sql', LOG_FLAG_NORMAL, [$sql, $args, $syslog_start_time, $syslog_end_time, $syslog_diff_time] );
                }
                
            }
            /******************END 监控代码**********************/
            
            $this->_lastSql = $sql;
            $this->_numRows = $stmt->rowCount();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt->closeCursor();
            
            return $results;
            
            
//         } 
//         catch (\PDOException $e) {
//             $e->queryString = $sql;
//             $e->traceAsString = $e->getTraceAsString();
//             throw $e;
//         }
    }

    /**
     * 执行非查询语句
     * @param string $sql
     * @throws \Exception
     * @return number
     * TODO: 拆分执行过多逻辑
     */
    public function execute( $sql, $args=null ){
        $this->connect( );
        
        /******************START 监控代码********************/
        if (!empty($this->monitor)) {
        	$start = microtime(TRUE);
        }
        /******************END 监控代码**********************/
        
        $stmt = $this->_conn->prepare($sql);
        
        if($args!=null && sizeof($args)>0)
            $this->_bindParams($stmt, $args);
        
        $stmt->execute();
        
        /******************START 监控代码********************/
        if (!empty($this->monitor)) {
            $end = microtime(TRUE);
            $this->monitor->log($stmt, $args, $end - $start);
        }
        /******************END 监控代码**********************/
        
        $this->_lastSql = $sql;
        $this->_numRows = $stmt->rowCount();
        
        $this->_lastInsertId = $this->_conn->lastInsertId();
        
        return $this->_numRows;
    }

    
    /**
     * 启动事务
     * @access public
     * @return void
     */
    public function startTrans( ){
        $this->connect( );
        
        // 数据rollback 支持
        if( $this->_transTimes == 0 ) {
        	$this->_conn->beginTransaction();
        }
        $this->_transTimes++;
        
        return;
    }

    /**
     * 用于非自动提交状态下面的查询提交
     * @access public
     * @return boolen
     */
    public function commit( ){
        if( $this->_transTimes > 0 ) {
        	$result = $this->_conn->commit();
            $this->_transTimes = 0;
            if( !$result )
                throw new \Exception( '提交事务时出现了错误. error:' . $this->_conn->errorInfo() );
        }
        return true;
    }

    /**
     * 事务回滚
     * @access public
     * @return boolen
     */
    public function rollback( ){
        if( $this->_transTimes > 0 ) {
        	$result = $this->_conn->rollBack();
            $this->_transTimes = 0;
            if( !$result )
                throw new \Exception( '回滚事务时出现了错误. error:' . $this->_conn->errorInfo() );
        }
        return true;
    }
    
    /**
     * 关闭数据库
     * @access public
     * @return void
     */
    public function close( ){
        $this->_conn = null;
        $this->_connected = false;
    }
    
    /**
     * 返回最后插入的ID
     * @access public
     * @return string
     */
    public function getLastInsID( ){
        return $this->_lastInsertId;
    }

    /**
     * 返回最后执行的sql语句
     * @access public
     * @return string
     */
    public function getLastSql( ){
        return $this->_lastSql;
    }
    
    /**
     * 返回数据库的错误信息
     * @access public
     * @return string
     */
    public function getDbError( ){
        return $this->_conn->errorInfo();
    }
    
    /**
     * Binding multiple variables to prepare statement
     * @param PDOStatement $stmt PDOStatement instance
     * @param $params parameters array
     */
    private function _bindParams(\PDOStatement &$stmt, $params) {
    	foreach ($params as $field => $val) {
    		$$field = $val;
			$stmt->bindParam(":$field", $$field);    		
    	}
    }
}

/**
 * 数据库连接类
 * @author 郭尔基(guoerji@vacn.com.cn)
 */
class DBConnection extends PDO {

	private $_dsn;
	private $_attr=array();

	public function __construct($db_type, $config) {
		$this->_type = $db_type;
		$this->_config = $config;
		
		$this->_setDSN();
		// \System\Logs::info( '_debug.sql1', LOG_FLAG_NORMAL, ['111111',$this->_config['USER'],$this->_config['PWD'],$this->_attr] );
		parent::__construct($this->_dsn, $this->_config['USER'], $this->_config['PWD'], $this->_attr);
		
		$this->setAttributes(array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	}

	private function _setDSN() {
		switch ($this->_type) {                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
			case 'MYSQL':
				$host = isset($this->_config['HOST']) ? $this->_config['HOST'] : '127.0.0.1';
				$port = isset($this->_config['PORT']) ? $this->_config['PORT'] : '3306';
				$this->_dsn = 'mysql:host='. $host .';dbname='. $this->_config['NAME'] .';port='. $port;
				// \System\Logs::info( '_debug.sql', LOG_FLAG_NORMAL, [$this->_dsn,$this->_config['HOST']] );
				$persistent = isset($this->_config['PERSIST']) ? boolval($this->_config['PERSIST']) : false;
				if ($persistent) { $this->_attr[PDO::ATTR_PERSISTENT] = true; }
				break;

			case 'SQLITE':
				$this->_dsn = 'sqlite:'. $this->_config['PATH'];
				break;

			default:break;
		}
	}
	
	private function _parseConnectionParam() {} 
	
	public function setAttributes($settings) {
		foreach ($settings as $key => $val) {
			$this->setAttribute($key, $val);
		}
	}
	
}

?>
