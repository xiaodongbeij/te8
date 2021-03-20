
    $(".aply").click(function(){
			var reg_realName=/^(?=.*\d.*\b)/;
			var reg_phone=/^(\d{5}|\d{6}|\d{7}|\d{8}|\d{9}|\d{10}|\d{11}|\d{12}|\d{13}|\d{14}|\d{15}|\d{16}|\d{17}|\d{18}|\d{19}|\d{20}|\d{21})$/;
			
			var reg_identity=/^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/;
            
			if($("#real_name").val()==""||reg_realName.test($("#real_name").val())==true){
                layer.msg("请正确填写真实姓名");
			}else if(reg_phone.test($("#mobile").val())==false){
                layer.msg("请正确输入手机号码");
			}else if($(".sf1").val()==""||$(".sf2").val()==""){
				layer.msg("请上传自拍照");
			}else{
                aiax();
            }
    })
    function aiax(){
        $.ajax({url:"/Appapi/Auth/authsave",
            dataType:"json",
            data:{
                uid:uid,
                token:token,
                real_name:$("#real_name").val(),
                mobile:$("#mobile").val(),
                cer_no:$("#cer_no").val(),
                front_view:$(".sf1").val(),
                back_view:$(".sf2").val(),
                // handset_view:$(".sf3").val()
            },
            type:"POST",
            success:function(data){
                    //console.log(data);
                    if(data.ret==200){
                        window.location.href="/Appapi/Auth/succ?uid="+uid;
                    }else{
                        layer.msg(data.msg);
                    }
            },
            error:function(e){
                    layer.msg(e.msg);
            }
        })

    }
    //身份证上传

    function file_click(e){
			var n= e.attr("data-index");
			upload(n);
    }
    function upload(index) {

			$('#upload').empty();
			var input = '<input type="file" id="ipt-file1" name="file"  accept="image/*"/><input type="file" id="ipt-file2" name="file"  accept="image/*"/><input type="file" id="ipt-file3" name="file"  accept="image/*"/>';
			$('#upload').html(input);
			var iptt=document.getElementById(index);
			if(window.addEventListener) { // Mozilla, Netscape, Firefox
					iptt.addEventListener('change',function(){
							ajaxFileUpload(index);
							var arr_img=new Array("/static/appapi/images/auth/identity_face.png","/static/appapi/images/auth/identity_back.png","/static/appapi/images/auth/identity_handle.png");
							var sub=index.substr(8,1);
							$(".img-sfz[data-index="+index+"]").attr("src",arr_img[sub-1]);
							$(".shadd[data-select="+index+"]").show();
					},false);
			}else{
					iptt.attachEvent('onchange',function(){
							ajaxFileUpload(index);
							var arr_img=new Array("/static/appapi/images/auth/identity_face.png","/static/appapi/images/auth/identity_back.png","/static/appapi/images/auth/identity_handle.png");
							var sub=index.substr(8,1);
							$(".img-sfz[data-index="+index+"]").attr("src",arr_img[sub-1]);
							$(".shadd[data-select="+index+"]").show();
					});
			}
			$('#'+index).click();
    }
    function ajaxFileUpload(img) {
    		var layer_index = layer.load(); //添加遮挡层
			$("."+img).css({"width":"0px"});
			$(".box-upload[data-index="+img+"]").hide();
			$("."+img).animate({"width":"100%"},700,function(){
					var id= img;
					var num=img.substr(8,1);
					
					$.ajax({url: "getuploadtoken", success: function(res){

						var resa=JSON.parse(res);
						var token = resa.token;
						var domain = resa.domain;
						var name = 'auth_'+uid+'_'+num+new Date().getTime()+'.jpg';
						var imgurl = qiniu_expedite_url+name; //加速域名模板上定义
						$.ajaxFileUpload({
							url: qiniu_upload_url, //模板上定义
							secureuri: false,
							fileElementId: id,
							data: { 'x:name':name,fname:name,key:name,token:token },
							dataType: 'json',
							success:function(data,status,xhr){
								//七牛不返回ajaxFileUpload可使用的错误提示，只能自行访问图片尝试
								console.log("上传成功");
								layer.close(layer_index); //删除遮挡层
								/*$.ajax({
									url : imgurl,
									async : false,
									type : 'HEAD',
									success:function(){*/
										var str=data;
										var sub=img.substr(8,1);
										$(".sf"+sub).attr("value",name);
										$(".shadd[data-select="+img+"]").hide();
										$(".box-upload[data-index="+img+"]").show();
										$(".box-upload[data-index="+img+"] img").attr("src","/static/appapi/images/auth/ok2.jpg");
									/*},
									error:function(e){
										console.log("访问图片失败");
										$(".shadd[data-select="+img+"]").hide();
										$(".box-upload[data-index="+img+"]").show();
										$(".box-upload[data-index="+img+"] img").attr("src","/static/appapi/images/auth/no2.jpg");
									}
								})*/
							},
							error:function(data,status,e){
								layer.close(layer_index); //删除遮挡层
								console.log("上传图片失败");
								console.log(data);
								console.log(status);
								console.log(e);
								$(".shadd[data-select="+img+"]").hide();
									$(".box-upload[data-index="+img+"]").show();
									$(".box-upload[data-index="+img+"] img").attr("src","/static/appapi/images/auth/no2.jpg");

							}

						})
					}


					});



					return true;
			});
    }