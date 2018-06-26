var JsonUti = {
            //���廻�з�
            n: "\n",
            //�����Ʊ��
            t: "\t",
            //ת��String
            convertToString: function(obj) {
                return JsonUti.__writeObj(obj, 1);
            },
            //д����
            __writeObj: function(obj    //����
                    , level             //��Σ�����Ϊ1��
                    , isInArray) {       //�˶����Ƿ���һ��������
                //���Ϊ�գ�ֱ�����null
                if (obj == null) {
                    return "null";
                }
                //Ϊ��ͨ���ͣ�ֱ�����ֵ
                if (obj.constructor == Number || obj.constructor == Date || obj.constructor == String || obj.constructor == Boolean) {
                    var v = obj.toString();
                    var tab = isInArray ? JsonUti.__repeatStr(JsonUti.t, level - 1) : "";
                    if (obj.constructor == String || obj.constructor == Date) {
                        //ʱ���ʽ��ֻ�ǵ�������ַ�����������Date����
                        return tab + ("\"" + v + "\"");
                    }
                    else if (obj.constructor == Boolean) {
                        return tab + v.toLowerCase();
                    }
                    else {
                        return tab + (v);
                    }
                }
 
                //дJson���󣬻����ַ���
                var currentObjStrings = [];
                //��������
                for (var name in obj) {
                    var temp = [];
                    //��ʽ��Tab
                    var paddingTab = JsonUti.__repeatStr(JsonUti.t, level);
                    temp.push(paddingTab);
                    //д��������
                    temp.push(name + " : ");
 
                    var val = obj[name];
                    if (val == null) {
                        temp.push("null");
                    }
                    else {
                        var c = val.constructor;
 
                        if (c == Array) { //���Ϊ���ϣ�ѭ���ڲ�����
                            temp.push(JsonUti.n + paddingTab + "[" + JsonUti.n);
                            var levelUp = level + 2;    //�㼶+2
 
                            var tempArrValue = [];      //����Ԫ������ַ�������Ƭ��
                            for (var i = 0; i < val.length; i++) {
                                //�ݹ�д����                         
                                tempArrValue.push(JsonUti.__writeObj(val[i], levelUp, true));
                            }
 
                            temp.push(tempArrValue.join("," + JsonUti.n));
                            temp.push(JsonUti.n + paddingTab + "]");
                        }
                        else if (c == Function) {
                            temp.push("[Function]");
                        }
                        else {
                            //�ݹ�д����
                            temp.push(JsonUti.__writeObj(val, level + 1));
                        }
                    }
                    //���뵱ǰ�������ԡ��ַ���
                    currentObjStrings.push(temp.join(""));
                }
                return (level > 1 && !isInArray ? JsonUti.n : "")                       //���Json�������ڲ�����Ҫ���и�ʽ��
                    + JsonUti.__repeatStr(JsonUti.t, level - 1) + "{" + JsonUti.n     //�Ӳ��Tab��ʽ��
                    + currentObjStrings.join("," + JsonUti.n)                       //������������ֵ
                    + JsonUti.n + JsonUti.__repeatStr(JsonUti.t, level - 1) + "}";   //��ն���
            },
            __isArray: function(obj) {
                if (obj) {
                    return obj.constructor == Array;
                }
                return false;
            },
            __repeatStr: function(str, times) {
                var newStr = [];
                if (times > 0) {
                    for (var i = 0; i < times; i++) {
                        newStr.push(str);
                    }
                }
                return newStr.join("");
            }
        };