//基于jquery的js校验
//rules是一个数组，包含 选择器 规则 参数 错误返回提示
function simpleVali(rules){
	for (var i = 0, len = rules.length; i < len; i++) {
		var data = $(rules[i][0]).val();
		if(data){
			data = data.trim()
		}
		switch(rules[i][1]){
			//后期可能要补一个传入文件 大小 和 后缀的检测
			case 'require':
				if(!data){
					return rules[i][3];
				}
				break;
			case 'max':
				if(data.length > rules[i][2]){
					return rules[i][3];
				}
				break;
			case 'min':
				if(data.length < rules[i][2]){
					return rules[i][3];
				}
				break;
			case 'mobile':
				if(!/^1[3-8]\d{9}$/.test(data)){
					return rules[i][3] ? rules[i][3] : '手机号码格式不正确';
				}
				break;
			case 'phone':
				if(!/^(([0-9]{2,3})|([0-9]{3}-))?((0[0-9]{2,3})|0[0-9]{2,3}-)?[1-9][0-9]{6,7}(-[0-9]{1,4})?$/.test(data)){
					return rules[i][3] ? rules[i][3] : '号码格式不正确';
				}
				break;
			case 'url':
				if(!/^((https?|http|ftp):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/.test(data)){
					return rules[i][3] ? rules[i][3] : '网址格式不正确';
				}
				break;
			case 'email':
				if(!/^(\w)+(\.\w+)*@(\w)+((\.\w{2,3}){1,3})$/.test(data)){
					return rules[i][3] ? rules[i][3] : '电子邮箱格式不正确';
				}
				break;
			case 'qq':
				if(!/^[1-9]([0-9]{5,11})$/.test(data)){
					return rules[i][3] ? rules[i][3] : 'qq格式不正确';
				}
				break;
			case 'number':
				if(!/^[0-9]+$/.test(data)){
					return rules[i][3] ? rules[i][3] : '必须为数字';
				}
				break;
			case 'integer':
				if(!/^[-+]?[0-9]+$/.test(data)){
					return rules[i][3] ? rules[i][3] : '必须为整数';
				}
				break;
			case 'regex':
				if(!rules[i][2].test(data)){
					return rules[i][3];
				}
				break;
		}
	}
}