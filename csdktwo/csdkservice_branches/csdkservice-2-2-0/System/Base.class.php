<?php

/*
 * | api 基类
 */

namespace System;

class Base extends Controller {

    protected $error;

    public function __construct() {
        parent::__construct();
        $this->error = C('ERROR');
    }

    /**
     * disposeImage
     * 处理图片为特定的格式
     * @param mixed $array
     * @param string $type banner goods_detail goods_list other_thumb
     * @access private
     * @return flase array
     *
     * 	'WAP_IMG_SIZE' => array(
      'banner' => array('width'=>300,'height'=>175),
      'goods_detail' => array('width'=>320,'height'=>320),
      'goods_list' => array('width'=>152,'height'=>152),
      'other_thumb' => array('width'=>106,'height'=>106),
      ),
     */
    protected function disposeImage(&$array, $type) {
        $sizeArrayData = C('WAP_IMG_SIZE');
        $sizeArray = $sizeArrayData[$type];
        $width = $sizeArray['width'];
        $height = $sizeArray['height'];
        if ($type == 'banner') {    # 处理分支 banner
            foreach ($array as $k => $v) {
                if (isset($v['rotator_img'])) { # 首页轮播处理分支
                    $returnUrl = $this->getAliyunThumb($v['rotator_img'], $width, $height);
                    $array[$k]['rotator_img'] = $returnUrl;
                } elseif (isset($v['cover_picture'])) { # 商品分类轮播处理分支
                    $returnUrl = $this->getAliyunThumb($v['cover_picture'], $width, $height);
                    $array[$k]['cover_picture'] = $returnUrl;
                }
            }
        } elseif ($type == 'goods_list') { # 处理分支 goods_list
            foreach ($array as $k => $v) {
                $returnUrl = $this->getAliyunThumb($v['color_picture'], $width, $height);
                $array[$k]['color_picture'] = $returnUrl;
            }
        } elseif ($type == 'goods_detail') {  # 处理分支 goods_detail
            foreach ($array as $k => $v) {
                $returnUrl = $this->getAliyunThumb($v['color_picture'], $width, $height);
                $array[$k]['color_picture'] = $returnUrl;
                $returnUrl = $this->getAliyunThumb($v['goods_img'], $width, $height);
                $array[$k]['goods_img'] = $returnUrl;

                # 处理商品详情页
                $returnData = $this->disposeDesc($v['goods_desc'], $width, $height);
                $array[$k]['goods_desc'] = $returnData;
            }
        } elseif ($type == 'goods_detail_attr') { # 处理分支 goods_detail_attr
            foreach ($array as $k => $v) {
                $returnUrl = $this->getAliyunThumb($v['picture'], $width, $height);
                $array[$k]['picture'] = $returnUrl;
            }
        } elseif ($type == "other_thumb") {  # other_thumb 其他图片处理分支
            foreach ($array as $k => $v) {
                $returnUrl = $this->getAliyunThumb($v['goods_img'], $width, $height);
                $array[$k]['goods_img'] = $returnUrl;
            }
        } elseif ($type == "order") {   # order 订单图片处理分支
            foreach ($array as $k => $v) {
                foreach ($v as $kk => $vv) {
                    foreach ($v['goods'] as $k2 => $v2) {
                        if (isset($v2['goods_img'])) {
                            $returnUrl = $this->getAliyunThumb($v2['goods_img'], $width, $height);
                            $array[$k]['goods'][$k2]['goods_img'] = $returnUrl;
                        }
                    }
                }
            }
        }
    }

    /**
     * disposeDesc
     * 处理商品详情页
     * @param mixed $text
     * @access protected
     * @return void
     */
    protected function disposeDesc($text, $width, $height) {
        # 正则匹配
        $preg = '/http:\/\/cdn\.xf9\.com\/(.*?)(\.[jpegn]{3,5})/';
        $suffix = "@1e_{$width}w_0c_0i_1o_100Q_1x";
        $host = 'http://' . C('OSS_ALIYUN_IMG_DISPOSE_DOMAIN') . '/';

        # 替换
        return preg_replace($preg, $host . "\\1" . "\\2" . $suffix . "\\2", $text);
    }

    /**
     * getAliyunThumb
     *
     * 获取云端url 返回云端的缩略图路径
     *
     * @url 云端原始图片的路径
     *
     * @px 云端的缩略图尺寸
     *
     * demo:
     *
     * http://pinke52.oss-cn-hangzhou.aliyuncs.com/5327aac2b3b1d.jpg
     *
     * return demo:
     *
     * http://pinke52.img-cn-hangzhou.aliyuncs.com/5327a9986e9fa.jpg@1e_75w_75h_0c_0i_1o_100Q_1x.jpg
     *
     * @access public
     *
     * @return $url
     */
    protected function getAliyunThumb($url, $width, $height) {
        $img_host = C('OSS_ALIYUN_IMG_DISPOSE_DOMAIN');
        if (strpos($url, 'http://') === 0) {
            $keys = explode('//', $url);
            $keys = explode('/', $keys[1]);
        } else {
            $keys = explode('/', $url);
        }
        unset($keys[0]);
        $key = implode('/', $keys);
        $suffix = end(explode('.', $key));
        $thumb = 'http://' . $img_host . '/' . $key . "@1e_{$width}w_{$height}h_0c_0i_1o_100Q_1x." . $suffix;
        return $thumb;
    }

    /**
     * 生成预览key
     *
     */
    protected function genReviewKey($id, $uid, $sign) {
        $key = C('PREVIEW_KEY');
        $sever_sign = md5(sha1($key . $uid) . $id);
        if ($sever_sign == $sign) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * push_express
     * 物流推送公共方法
     * @param mixed $com 快递公司代码
     * @param mixed $express_number 快递单号
     * @param mixed $order_id 订单id
     * @param tinyint order_type 订单类型  1 普通子订单 2 退货订单
     * @return void
     */
    public function push_express($com, $express_number, $order_id, $type = 0, $order_type = 1) {
        # 实例化快递操作类
        $redis = R();
        # 拼凑初始化数据
        $data['com'] = $com;
        $data['invoice_no'] = $express_number;
        $data['order_id'] = $order_id;
        $data['type'] = $order_type;

        # 初始化数据表
        $find_data = $this->executeSingleTableQuery('16860_express_info', 'id', array('order_id' => $order_id, 'type' => $order_type));
        $find_data = $find_data[0];
        if ($find_data) {

            $this->executeSingleTableUpdate('16860_express_info', $data, array('id'=>$find_data['id']));
            $id = $find_data['id'];
        } else {
            $this->executeSingleTableCreate('16860_express_info', $data);
            $id = $this->getLastInsertId();
        }
        # 判断快递单号是否存在 如果存在则不推送
        $flag = get_hash_value($redis, 'express', $com . $express_number);
        if ($flag) {
            return array('error' => 0);
        }
        if ($type == 1) {
            if (empty($id)) {
                return array('error' => 1, 'message' => '初始化物流信息失败!');
            }
        } else {
            if (empty($id)) {
                return array('error' => 1, 'message' => '初始化物流信息失败!');
            }
        }
        $params = array(
            'code'=>$com,
            'number'=>$express_number,
            'arguments'=>'id='.$id,
        );
//        $res = request_core_api('Express.poll',$params);
//        $res = $res['response'];
//        # 发起推送请求
//        
//        if (!$res) {
//           // $text = $express->getError();
//            if ($type == 1) {
//                $data = array('error' => 1, 'message' => $text);
//                return $data;
//            } else {
//                return array('error' => 0, 'message' => $text);
//            }
//        }
        set_hash_value($redis, 'express', $com . $express_number, $id);
        return array('error' => 0);
    }

}

?>
