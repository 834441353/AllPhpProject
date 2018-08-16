var time = 0;
var isSend = false;
$(function(){
	//修改密码
    $('#myorm').validator({
            fields: {
                newPass: {
                  rule:"required;length[6~20]",
                  msg:{required:"请输入新密码"},
                  tip:"请输入新密码"
                },
                reNewPass: {
                  rule:"required;length[6~20];match[newPass]",
                  msg:{required:"请再次输入新密码",match:"两次输入密码不匹配"},
                  tip:"请再次输入新密码"
                },
            },
          valid: function(form){
            var params = WST.getParams('.ipt');
            var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
            $.post(WST.U('home/users/passEdit'),params,function(data,textStatus){
              layer.close(loading);
              var json = WST.toJson(data);
              if(json.status=='1'){
                  WST.msg("操作成功",{icon:1});
        	        setTimeout(function(){ 
        	        	location.href=WST.U('home/users/security');
        	  	    },2000);
              }else{
                    WST.msg(json.msg,{icon:2});
              }
            });
      }
    })
    //绑定邮箱
    $('#emailForm').validator({
    	rules: {
            remote: function(element){
            	return $.post(WST.U('home/users/checkLoginKey'),{"loginName":element.value},function(data,textStatus){
            	});
            }	
    	},
        fields: {
        	userEmail: {
		        rule:"required;email;remote;",
		        msg:{required:"请输入邮箱",email:"请输入有效的邮箱"},
		        tip:"请输入邮箱",
            },
            verifyCode: {
	            rule:"required",
	            msg:{required:"请输入验证码"},
	            tip:"请输入验证码",
	            target:"#verify"
            }
        },
        
      valid: function(form){
        var params = WST.getParams('.ipt');
        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
        $.post(WST.U('home/users/getEmailVerify'),params,function(data,textStatus){
          layer.close(loading);
          var json = WST.toJson(data);
          if(json.status=='1'){
  			WST.msg('邮箱已发送，请注册查收');
  	        setTimeout(function(){ 
  	          $('#emailForm').hide();
  	          $('#inemail').html($('#userEmail').val());
  	          $('#prompt').show();
  	        },1000);
          }else{
                WST.msg(json.msg,{icon:2});
                WST.getVerify('#verifyImg');
          }
        });
      }
    });
	if(isSend )return;
	isSend = true;
    //修改邮箱
    $('#getemailForm').validator({
      valid: function(form){
        var params = WST.getParams('.ipt');
        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
        $.post(WST.U('home/users/getEmailVerify2'),params,function(data,textStatus){
          layer.close(loading);
          var json = WST.toJson(data);
          if(json.status=='1'){
  			WST.msg('邮箱已发送，请注册查收');
			time = 120;
			$('#timeSend1').attr('disabled', 'disabled').css('background','#e8e6e6');
			$('#timeSend1').html('发送验证邮件(120)').css('width','120px');
			var task = setInterval(function(){
				time--;
				$('#timeSend1').html('发送验证邮件('+time+")");
				if(time==0){
					isSend = false;						
					clearInterval(task);
					$('#timeSend1').html("重新发送验证邮件").css('width','100px');
					$('#timeSend1').removeAttr('disabled').css('background','#e23e3d');
				}
			},1000);
          }else{
                WST.msg(json.msg,{icon:2});
                WST.getVerify('#verifyImg');
          }
        });
      }
    });
    //绑定手机号
    $('#phoneForm').validator({
      valid: function(form){
        var me = this;
        // ajax提交表单之前，先禁用submit
        me.holdSubmit();
        var params = WST.getParams('.ipt');
        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
        $.post(WST.U('home/users/phoneEdit'),params,function(data,textStatus){
          layer.close(loading);
          var json = WST.toJson(data);
          if(json.status=='1'){
              WST.msg("绑定手机成功",{icon:1});
  	          setTimeout(function(){ 
  	        	  location.href=WST.U('home/users/editPhoneSu','pr='+json.process);
	  	      },2000);
          }else{
                WST.msg(json.msg,{icon:2});
                WST.getVerify('#verifyImg');
          }
        });
      }
    });
    //修改手机号
    $('#getphoneForm').validator({
      valid: function(form){
        var params = WST.getParams('.ipt');
        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
        $.post(WST.U('home/users/phoneEdit2'),params,function(data,textStatus){
          layer.close(loading);
          var json = WST.toJson(data);
          if(json.status=='1'){
              WST.msg("验证成功",{icon:1});
  	          setTimeout(function(){
  	        	location.href=WST.U('home/users/editPhoneSu2');
	  	      },2000);
          }else{
                WST.msg(json.msg,{icon:2});
                WST.getVerify('#verifyImg');
          }
        });
      }
    });
    $('#phoneVerify').validator({
      valid: function(form){
    	  var id=$('#VerifyId').val();
    	  getPhoneVerifys(id);
      }
    });
})
//发送手机验证码
function getPhoneVerify(id){
	if(!$('#userPhone').isValid())return;
	$('#VerifyId').val(id);
	if(window.conf.SMS_VERFY==1){
		WST.open({type: 1,title:"请输入验证码",shade: [0.6, '#000'],border: [0],content: $('#phoneVerify'),area: ['600px', '180px']});
	}else{
		getPhoneVerifys(id);
	}
}
function getPhoneVerifys(id){
	WST.msg('正在发送短信，请稍后...',{time:600000});
	var time = 0;
	var isSend = false;
	var params = WST.getParams('.ipt');
	$.post(WST.U('home/users/getPhoneVerify'+id),params,function(data,textStatus){
		var json = WST.toJson(data);
		$('#Checkcode').val(json.msg);//测试
		if(isSend )return;
		isSend = true;
		if(json.status!=1){
			WST.msg(json.msg, {icon: 5});
			WST.getVerify('#verifyImg');
			time = 0;
			isSend = false;
		}if(json.status==1){
			WST.msg('短信已发送，请注册查收');
			layer.closeAll('page'); 
			time = 120;
			$('#timeObtain').attr('disabled', 'disabled').css('background','#e8e6e6');
			$('#timeObtain').html('获取手机验证码(120)').css('width','130px');
			var task = setInterval(function(){
				time--;
				$('#timeObtain').html('获取手机验证码('+time+")");
				if(time==0){
					isSend = false;						
					clearInterval(task);
					$('#timeObtain').html("重新获取验证码").css('width','100px');
					$('#timeObtain').removeAttr('disabled').css('background','#e23e3d');
				}
			},1000);
		}
	});
}
