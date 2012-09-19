<?php
	Sys::importLib("Forms", true);
		/*
		View::jsBatchAdd( 
			Forms::urlControls("TextINput,TextArea,CheckBox,Radio,ButtonSubmit,TextArea, Select"),
			Forms::urlTemplate("labels-left")
		);
		*/
		
		?>
		<div id="form-container"></div>
		
		<div id="ajform-container"></div>
		
		<script>
			var controls = [];
			controls.push({
				type:"TextInput",
				name:"email",
				config:{
					label:"Email",
					value:"gabriel.dodan@gmail.com"
				}
			});
			
			controls.push({
				type:"TextInput",
				name:"name",
				config:{
					label:"Name",
					value:"Gabriel Dodan",
					attrs:{
						onclick:'onClickHandler(event)',
						style:"border:1px solid red"
					},
					binds:{
						click:function(){alert("2")}
					},
					validators:{}
				}
			});
			
			controls.push({
				type:"CheckBox",
				name:"agree",
				config:{
					label:"Agree",
					value:"yes",
					attrs:{
						/*onclick:"cbOnClick()"*/
					},
					binds:{
						click:function(){
							$.post(
								'<?php echo Locator::urlHome() . "/tests/ajaxform" ?>', 
								{}, 
								function(data){
									//document.write(data);
									console.log(data);
									//$("#ajform-container").append(data);
									//document.getElementById("ajform-container").innerHTML = data;
									//document.getElementById("form-container").appendChild(document.getElementById("ajform-container"));
								}, 
								'html'
							);
							/*
							if ( LoginForm.getControl('agree').isChecked() ) {
								LoginForm.getControl('detalii').hide();
							}
							else {
								LoginForm.getControl('detalii').show();
							}
							*/
						}
					}
				}
			});
			controls.push({
				type:"Radio",
				name:"radio_agree",
				config:{
					label:"Radio Agree",
					value:"yesyes",
					attrs:{
						onclick:"radioOnClick()"
					},
					binds:{
						click:function(){
							//console.log(LoginForm['agree'].getValue());
							//console.log(LoginForm.getControl('radio_agree').getValue());
							//console.log(this);
							//LoginForm.getControl('detalii').disable();
						}
					}
				}
			});
			controls.push({
				type:"TextArea",
				name:"detalii",
				config:{
					label:"Detalii",
					value:"Detalii content<a href=\"\">wqeqw</a>'fsfsdff'",
					attrs:{
						rows:10,
						cols:50
					},
					binds:{
					}
				}
			});
			
			controls.push({
				type:"Select",
				name:"tari",
				config:{
					label:"Tari",
					dataSet:[['ro',"Romania"], ['en', "Anglia"], ['fr', 'Franta']],
					selectedValues:['en'],
					itemsRenderer: function(control) {
						var items = control.items;
						var content = [];
						for (var i=0; i<items.length; i++) {
							if ( items[i].value == 'fr' ) {
								items[i].attrs = {style:"color:red;"};
							}
							content.push( items[i].content() );
						}
						return content.join("\n");
					},
					attrs:{
						style:"width:200px;"
					},
					binds:{
						change:function() {
							alert(LoginForm.getControl('tari').getValue());
						}
					}
				}
			});
			
			controls.push({
				type:"DatePicker",
				name:"zi",
				config:{
					label:"Zi",
					value:""
				}
			});
			
			controls.push({
				type:"ButtonSubmit",
				name:"submit",
				config:{
					label:"",
					value:"Send"
				}
			});
			
			
			function onClickHandler(event) {
				alert("1");
			}
			function cbOnClick() {
				console.log(LoginForm.getControl('agree').getValue());
			}
			function radioOnClick() {
				console.log(LoginForm.getControl('radio_agree').getValue());
			}
			
			var LoginForm = new Pd.Forms.Form({
					name:"LoginForm",
					template:"labels-left",
					renderTo:"form-container"
				},
				controls
			);
		</script>
		
		<!--
		document.getElementById("form-container").innerHTML='_' + '<style>' + data + '</style>' 
		document.getElementById("form-container").removeChild(document.getElementById("form-container").firstChild);
		-->
		
		<?php
