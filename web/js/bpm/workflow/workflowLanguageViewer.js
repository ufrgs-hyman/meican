var imagePath = '../../images/workflow/';

var iconPath = '../../images/workflow/'+ language + '/';

var domains = domains ? domains : [];
var domains_keys = [], domains_values = [];
for (k in domains) {
	domains_keys.push(k);
	domains_values.push(domains[k]);
}

var users = users ? users : [];
var users_keys = [], users_values = [];
for (k in users) {
	users_keys.push(k);
	users_values.push(users[k]);
}

var admins = admins ? admins : [];
var admins_keys = [], admins_values = [];
for (k in admins) {
	admins_keys.push(k);
	admins_values.push(admins[k]);
}

var groups = groups ? groups : [];
var groups_keys = [], groups_values = [];
for (k in groups) {
	groups_keys.push(k);
	groups_values.push(groups[k]);
}

var devices = devices ? devices : [];
var devices_keys = [], devices_values = [];
for (k in devices) {
	devices_keys.push(k);
	devices_values.push(devices[k]);
}

var owner_domains = owner_domains ? owner_domains : [];
var owner_keys = [], owner_values = [];
for (k in owner_domains) {
    owner_keys.push(k);
    owner_values.push(owner_domains[k]);
}

var hours = ["00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23"];
var minutes = ["00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","34","35","36","37","38","39","40","41","42","43","44","45","46","47","48","49","50","51","52","53","54","55","56","57","58","59"];
var weekday_values = [tt('Sunday'), tt('Monday'), tt('Tuesday'), tt('Wednesday'), tt('Thursday'), tt('Friday'), tt('Saturday')];
var weekday_keys = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

var workflowLanguageViewer = {
	
    //Set a unique name for the language
    languageName: "workflows",
    
    accordionViewParams: {
    },
    
    layoutOptions: {
        units: [
            
        //Botões superiores
        {

        },

        {

        },

        {
            position: 'center', 
            body: 'center', 
            gutter: '2px',
            left: 0
        }
        ]
    },
    
    layerOptions: {
        layerMap: false
    },

    //inputEx fields for pipes properties
    propertiesFields: [
    {
        "type": "string", 
        inputParams: {
            name: "name", 
            label: tt('Workflow Name:'),
            typeInvite: tt("Enter a name"),
            cols: 5,
        }
    },
    {
    	type: "hidden",
        inputParams: {
			name: "domains_owner",                 
			value: owner_keys[0],
        }
    }
],

//List of node types definition
modules: [
       
	{	name: "New_Request",
	    container: {
	    	xtype:"WireIt.MeicanContainer", 
	        icon: iconPath + "ico_request.png",
	        image: imagePath + "request.png",
	        propertiesForm: [],

	        
	        terminals: [
	        {	name: "_OUTPUT",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
	            direction: [1,0],
	            nMaxWires: "1",
	            wireConfig: {"drawingMethod": "arrows"},
	            offsetPosition: {
	                left: 35, 
	                top: 9
	            }
	        }
	        ],
	        
		    fields: []
	    }
	},


	{	name: "Domain",
	    container: {
	    	xtype:"WireIt.MeicanContainer", 
	        icon: iconPath + "ico_domain.png",
	        image: imagePath + "domain.png",
	        propertiesForm: [],
		   				
	        terminals: [
	        {	name: "_INPUT",
	        	ddConfig: {
	        	      type: "input",
	        	      allowedTypes: ["output"]
	        	},
	        	nMaxWires: "1",
	        	wireConfig: {"drawingMethod": "arrows"},
	           	direction: [-1,0],
	            offsetPosition: {
	                left: -15, 
	                top: 9
	            }
	        },
	
	        {	name: "_OUTPUT_YES",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
	        	direction: [1,0],
	        	nMaxWires: "1",
	        	wireConfig: { "drawingMethod": "arrows"},
	        	offsetPosition: {
	        		left: 55, 
	        		top: -3
	        	}
	        },
	        
	        {	name: "_OUTPUT_NO",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
			    direction: [1,1],
			    nMaxWires: "1",
			    wireConfig: { "drawingMethod": "arrows"},
			    offsetPosition: {
			        left: 55, 
			        top: 21
			    }
	        }
	        ],
	        		
	        fields: [
	        {	type: "inplaceedit", 
			    inputParams: {
			        name: "post",
			        editorField: {
			            type: "group", 
			            inputParams: {
			                fields:[
				                {
				                	type: "select", 
				        			inputParams: {
				        				label: "", 
				        				name: "dom_operator",
				        				selectValues: ["source", "previous", "next", "destination"],
				                        selectOptions: [tt("source"), tt("previous"), tt("next"), tt("destination")]
				        			}
				                },

				                {	
				                	type: "select", 
				        			inputParams: {
				        				label: "", 
				        				name: "value", 
				        				selectValues: domains_keys,
				                        selectOptions: domains_values
				        			}
				                }
			                ]
			            }
			        },
			        visu: {
					    visuType: 'func', 
					    func: function(val) {
					        //console.debug(val);
					        return val.dom_operator + ": " + val.value;
					    }
					},
	        		animColors:{
	        			from:"#FFFF99",
	        			to:"#DDDDFF"
	        		}
	        	}
	        }
	        ],
	    }
	},

	
	{	name: "User",
		container: {
			icon: iconPath + "ico_user.png",
			xtype: "WireIt.MeicanContainer",
			image: imagePath + "user.png",
			propertiesForm: [],
	   				
			terminals: [
	        {	name: "_INPUT",
	        	ddConfig: {
	        	      type: "input",
	        	      allowedTypes: ["output"]
	        	},
	        	nMaxWires: "1",
	        	wireConfig: {"drawingMethod": "arrows"},
	           	direction: [-1,0],
	            offsetPosition: {
	                left: -15, 
	                top: 9
	            }
	        },
	
	        {	name: "_OUTPUT_YES",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
	        	direction: [1,0],
	        	nMaxWires: "1",
	        	wireConfig: { "drawingMethod": "arrows"},
	        	offsetPosition: {
	        		left: 55, 
	        		top: -3
	        	}
	        },
	        
	        {	name: "_OUTPUT_NO",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
			    direction: [1,1],
			    nMaxWires: "1",
			    wireConfig: { "drawingMethod": "arrows"},
			    offsetPosition: {
			        left: 55, 
			        top: 21
			    }
	        }
	        ],
	
			fields: [
			{	type: "inplaceedit", 
				inputParams: {
					name: "post",
					editorField:{
						type: "select", 
						inputParams: 
						{	label: "", 
							name: "title", 
							selectValues: users_keys,
	                        selectOptions: users_values
						}
					},
					animColors:{
						from:"#FFFF99", 
						to:"#DDDDFF"
					}
				}
			}, 		
			]
		}
	},
	
	{	name: "Group",
		container: {
			xtype:"WireIt.MeicanContainer", 
			image: imagePath + "group.png",
			icon: iconPath + "ico_group.png",

			terminals: [
	        {	name: "_INPUT",
	        	ddConfig: {
	        	      type: "input",
	        	      allowedTypes: ["output"]
	        	},
	        	nMaxWires: "1",
	        	wireConfig: {"drawingMethod": "arrows"},
	           	direction: [-1,0],
	            offsetPosition: {
	                left: -15, 
	                top: 9
	            }
	        },
	
	        {	name: "_OUTPUT_YES",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
	        	direction: [1,0],
	        	nMaxWires: "1",
	        	wireConfig: { "drawingMethod": "arrows"},
	        	offsetPosition: {
	        		left: 55, 
	        		top: -3
	        	}
	        },
	        
	        {	name: "_OUTPUT_NO",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
			    direction: [1,1],
			    nMaxWires: "1",
			    wireConfig: { "drawingMethod": "arrows"},
			    offsetPosition: {
			        left: 55, 
			        top: 21
			    }
	        }
	        ],
	        
	        fields: [
	        {	type: "inplaceedit", 
			    inputParams: {
			        name: "post",
			        editorField:{
			            type: "select", 
			            inputParams: 
			            {	label: "", 
			                name: "title", 
			                selectValues: groups_keys,
	                        selectOptions: groups_values
			            }
			        },
			        animColors:{
			        	from:"#FFFF99",
			        	to:"#DDDDFF"
			        }
			    }
	        }, 		
	        ],
		}
	},
	
	{	name: "Device",
	    container: {
	    	xtype:"WireIt.MeicanContainer", 
	        icon: iconPath + "ico_device.png",
	        image: imagePath + "device.png",
	        propertiesForm: [],
		   				
	        terminals: [
	        {	name: "_INPUT",
	        	ddConfig: {
	        	      type: "input",
	        	      allowedTypes: ["output"]
	        	},
	        	nMaxWires: "1",
	        	wireConfig: {"drawingMethod": "arrows"},
	           	direction: [-1,0],
	            offsetPosition: {
	                left: -15, 
	                top: 9
	            }
	        },
	
	        {	name: "_OUTPUT_YES",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
	        	direction: [1,0],
	        	nMaxWires: "1",
	        	wireConfig: { "drawingMethod": "arrows"},
	        	offsetPosition: {
	        		left: 55, 
	        		top: -3
	        	}
	        },
	        
	        {	name: "_OUTPUT_NO",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
			    direction: [1,1],
			    nMaxWires: "1",
			    wireConfig: { "drawingMethod": "arrows"},
			    offsetPosition: {
			        left: 55, 
			        top: 21
			    }
	        }
	        ],
	        		
	        fields: [
	     	{	type: "inplaceedit", 
	     		inputParams: {
	     			name: "post",
 			        editorField:{
 			            type: "select", 
 			            inputParams: 
 			            {	label: "", 
 			                name: "title", 
 			                selectValues: devices_keys,
 	                        selectOptions: devices_values
 			            }
 			        },
 			        animColors:{
 			        	from:"#FFFF99",
 			        	to:"#DDDDFF"
 			        }
 			    }
	     	},
	     	],
	    }
	},
	
	{	name: "Bandwidth",
		container: {
			xtype:"WireIt.MeicanContainer", 
			image: imagePath + "bandwidth.png",
			icon: iconPath + "ico_bandwidth.png",
		
			terminals: [
	        {	name: "_INPUT",
	        	ddConfig: {
	        	      type: "input",
	        	      allowedTypes: ["output"]
	        	},
	        	nMaxWires: "1",
	        	wireConfig: {"drawingMethod": "arrows"},
	           	direction: [-1,0],
	            offsetPosition: {
	                left: -15, 
	                top: 9
	            }
	        },
	
	        {	name: "_OUTPUT_YES",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
	        	direction: [1,0],
	        	nMaxWires: "1",
	        	wireConfig: { "drawingMethod": "arrows"},
	        	offsetPosition: {
	        		left: 55, 
	        		top: -3
	        	}
	        },
	        
	        {	name: "_OUTPUT_NO",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
			    direction: [1,1],
			    nMaxWires: "1",
			    wireConfig: { "drawingMethod": "arrows"},
			    offsetPosition: {
			        left: 55, 
			        top: 21
			    }
	        }
	        ],

			fields: [
			{	type: "inplaceedit", 
			    inputParams: {
			        name: "post",
			        editorField: {
			            type: "group", 
			            inputParams: {
			                fields:[
			                {	type: "select", 
			                    inputParams: {
			                        name: "operator", 
			                        selectValues: ["== ","< ","<= ","> ",">= "],
			                    }
			                },

			                {	
			                	inputParams: {
			                		name: "bandwidth", 
			                		"required": true
			                	}
			                }
			                ]
			            }
			        },
					visu: {
					    visuType: 'func', 
					    func: function(val) {
					        //console.debug(val);
					        return val.operator + val.bandwidth + " Mbps";
					    }
					},
					animColors:{
					    from:"#FFFF99", 
					    to:"#DDDDFF"
					},
			    }
			}
			]
		}
	},
	
	{	name: "Duration",
		container: {
			xtype:"WireIt.MeicanContainer", 
			image: imagePath + "duration.png",
			icon: iconPath + "ico_duration.png",
		
			terminals: [
	        {	name: "_INPUT",
	        	ddConfig: {
	        	      type: "input",
	        	      allowedTypes: ["output"]
	        	},
	        	nMaxWires: "1",
	        	wireConfig: {"drawingMethod": "arrows"},
	           	direction: [-1,0],
	            offsetPosition: {
	                left: -15, 
	                top: 9
	            }
	        },
	
	        {	name: "_OUTPUT_YES",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
	        	direction: [1,0],
	        	nMaxWires: "1",
	        	wireConfig: { "drawingMethod": "arrows"},
	        	offsetPosition: {
	        		left: 55, 
	        		top: -3
	        	}
	        },
	        
	        {	name: "_OUTPUT_NO",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
			    direction: [1,1],
			    nMaxWires: "1",
			    wireConfig: { "drawingMethod": "arrows"},
			    offsetPosition: {
			        left: 55, 
			        top: 21
			    }
	        }
	        ],
		
			fields: [
			{	type: "inplaceedit", 
				inputParams: {
					name: "post",
			        editorField: {
			            type: "group", 
			            inputParams: {
			                fields:[
							{	type: "select", 
							    inputParams: {
							        name: "operator", 
							        selectValues: ["== ","< ","<= ","> ",">= "],
							    }
							},
							{	
								inputParams: {
									name: "duration", 
									"required": true
								}
							},
			                {	type: "select", 
			                    inputParams: {
			                        name: "unit",
			                        selectValues: ["minutes", "hours"],
			                        selectOptions: [tt("minutes"), tt("hours")],
			                    }
			                },
			                ]
			            }
			        },
					visu: {
					    visuType: 'func', 
					    func: function(val) {
					        //console.debug(val);
					        return val.operator + val.duration + " " + tt(val.unit);
					    }
					},
					animColors:{
					    from:"#FFFF99", 
					    to:"#DDDDFF"
					},
				}
			}
			]
		}
	},
	
	/*{	name: "WeekDay",
		container: {
			icon: iconPath + "ico_weekday.png",
			xtype: "WireIt.MeicanContainer",
			image: imagePath + "weekday.png",
			propertiesForm: [],
	   				
			terminals: [
	        {	name: "_INPUT",
	        	ddConfig: {
	        	      type: "input",
	        	      allowedTypes: ["output"]
	        	},
	        	nMaxWires: "1",
	        	wireConfig: {"drawingMethod": "arrows"},
	           	direction: [-1,0],
	            offsetPosition: {
	                left: -15, 
	                top: 9
	            }
	        },
	
	        {	name: "_OUTPUT_YES",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
	        	direction: [1,0],
	        	nMaxWires: "1",
	        	wireConfig: { "drawingMethod": "arrows"},
	        	offsetPosition: {
	        		left: 55, 
	        		top: -3
	        	}
	        },
	        
	        {	name: "_OUTPUT_NO",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
			    direction: [1,1],
			    nMaxWires: "1",
			    wireConfig: { "drawingMethod": "arrows"},
			    offsetPosition: {
			        left: 55, 
			        top: 21
			    }
	        }
	        ],
	
			fields: [
			{	type: "inplaceedit", 
				inputParams: {
					name: "post",
					editorField:{
						type: "select", 
						inputParams: 
						{	 
							name: "day",
							selectValues: weekday_keys,
	                        selectOptions: weekday_values
							
						}
					},
					animColors:{
						from:"#FFFF99", 
						to:"#DDDDFF"
					}
				}
			}, 		
			]
		}
	},*/
	
	/*{	name: "Hour",
		container: {
			xtype:"WireIt.MeicanContainer", 
			image: imagePath + "schedule.png",
			icon: iconPath + "ico_schedule.png",
		
			terminals: [
	        {	name: "_INPUT",
	        	ddConfig: {
	        	      type: "input",
	        	      allowedTypes: ["output"]
	        	},
	        	nMaxWires: "1",
	        	wireConfig: {"drawingMethod": "arrows"},
	           	direction: [-1,0],
	            offsetPosition: {
	                left: -15, 
	                top: 9
	            }
	        },
	
	        {	name: "_OUTPUT_YES",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
	        	direction: [1,0],
	        	nMaxWires: "1",
	        	wireConfig: { "drawingMethod": "arrows"},
	        	offsetPosition: {
	        		left: 55, 
	        		top: -3
	        	}
	        },
	        
	        {	name: "_OUTPUT_NO",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
			    direction: [1,1],
			    nMaxWires: "1",
			    wireConfig: { "drawingMethod": "arrows"},
			    offsetPosition: {
			        left: 55, 
			        top: 21
			    }
	        }
	        ],
		
			fields: [
			{
				type: "inplaceedit", 
			    inputParams: {
			        name: "post",
			        editorField: {
			            type: "group", 
			            inputParams: {
			                fields:[
		                        {
		                        	type: "combine",
									inputParams: {
										name: "init",
										fields:[
									        {
									        	type: "select", 
							                    inputParams: {
							                    	name: "inithour", 
							                        selectValues: hours,
							                    }
									        },
									        {
									        	type: "select", 
							                    inputParams: {
							                        name: "initminute", 
							                        selectValues: minutes,
							                    }
									        },
								        ],
										separators: [false,"h&nbsp;&nbsp;","m&nbsp;&nbsp;"],
									}
		                        },           
		                        {
		                        	type: "combine",
		                        	inputParams: {
		                        		name: "finish",
		                        		label: tt("to"),
		                        		fields:[
	                        		        {
                     		        		type: "select", 
                     		        		inputParams: {
							                        name: "finishhour", 
							                        selectValues: hours,
							                    }
	                        		        },
	                        		        {
	                        		        	type: "select", 
	                        		        	inputParams: {
							                        name: "finishminute", 
							                        selectValues: minutes,
							                    }
									         },
								         ],
								         separators: [false,"h&nbsp;&nbsp;","m&nbsp;&nbsp;"],
		                        	}
		                        },    
			                ],
			            }
			        },
					visu: {
					    visuType: 'func', 
					    func: function(val) {
					        //console.debug(val);
					        return val.init[0] + ":" + val.init[1] + " " + tt("to") + " " + val.finish[0] + ":" + val.finish[1];
					    }
					},
					animColors:{
					    from:"#FFFF99", 
					    to:"#DDDDFF"
					},
			    }
			}
			]
		}
	},*/	
	
	{	name: "Request_User_Authorization",
		container: {
			xtype:"WireIt.MeicanContainer", 
			image: imagePath + "request_user.png",
			icon: iconPath + "ico_request_user.png",

			terminals: [
	        {	name: "_INPUT",
	        	ddConfig: {
	        	      type: "input",
	        	      allowedTypes: ["output"]
	        	},
	        	nMaxWires: "1",
	        	wireConfig: {"drawingMethod": "arrows"},
	           	direction: [-1,0],
	            offsetPosition: {
	                left: -15, 
	                top: 9
	            }
	        },
	
	        {	name: "_OUTPUT_YES",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
	        	direction: [1,0],
	        	nMaxWires: "1",
	        	wireConfig: { "drawingMethod": "arrows"},
	        	offsetPosition: {
	        		left: 55, 
	        		top: -3
	        	}
	        },
	        
	        {	name: "_OUTPUT_NO",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
			    direction: [1,1],
			    nMaxWires: "1",
			    wireConfig: { "drawingMethod": "arrows"},
			    offsetPosition: {
			        left: 55, 
			        top: 21
			    }
	        }
	        ],
			
			fields: [
			{	type: "inplaceedit", 
			    inputParams: {
			        name: "post",
			        editorField:{
			            type: "select", 
			            inputParams: 
			            {	label: "", 
			                name: "title", 
			                selectValues: admins_keys,
	                        selectOptions: admins_values
			            }
			        },
					animColors:{
						from:"#FFFF99" , 
					    to:"#DDDDFF"
					}
			    }
			}, 			
			],
		}
	},

	
	{	name: "Request_Group_Authorization",
		container: {
			xtype:"WireIt.MeicanContainer", 
			image: imagePath + "request_group.png",
			icon: iconPath + "ico_request_group.png",

			terminals: [
	        {	name: "_INPUT",
	        	ddConfig: {
	        	      type: "input",
	        	      allowedTypes: ["output"]
	        	},
	        	nMaxWires: "1",
	        	wireConfig: {"drawingMethod": "arrows"},
	           	direction: [-1,0],
	            offsetPosition: {
	                left: -15, 
	                top: 9
	            }
	        },
	
	        {	name: "_OUTPUT_YES",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
	        	direction: [1,0],
	        	nMaxWires: "1",
	        	wireConfig: { "drawingMethod": "arrows"},
	        	offsetPosition: {
	        		left: 55, 
	        		top: -3
	        	}
	        },
	        
	        {	name: "_OUTPUT_NO",
	        	ddConfig: {
	        	      type: "output",
	        	      allowedTypes: ["input"]
	        	},
			    direction: [1,1],
			    nMaxWires: "1",
			    wireConfig: { "drawingMethod": "arrows"},
			    offsetPosition: {
			        left: 55, 
			        top: 21
			    }
	        }
	        ],
	        
	        fields: [
	        {	type: "inplaceedit", 
			    inputParams: {
			        name: "post",
			        editorField:{
			            type: "select", 
			            inputParams: 
			            {	label: "", 
			                name: "title", 
			                selectValues: groups_keys,
	                        selectOptions: groups_values
			            }
			        },
			        animColors:{
			        	from:"#FFFF99",
			        	to:"#DDDDFF"
			        }
			    }
	        }, 		
	        ],
		}
	},
	
	
	{	name: "Accept_Automatically",
	    container: {
	        icon: iconPath + "ico_accept.png",
	        xtype: "WireIt.MeicanContainer",
	        image: imagePath + "accept.png",
	        propertiesForm: [],
		   				
	        terminals: [
	        {	name: "_INPUT",
	        	ddConfig: {
	        	      type: "input",
	        	      allowedTypes: ["output"]
	        	},
	        	wireConfig: {"drawingMethod": "arrows"},
	            direction: [-1,0], 
	            offsetPosition: {
	                left: -15, 
	                top: 9
	            }
	        }
	        ],
	   				
	        fields: [],
	    }
	},
	
	
	{	name: "Deny_Automatically",
	    container: {
	        icon: iconPath + "ico_deny.png",
	        xtype: "WireIt.MeicanContainer",
	        image: imagePath + "deny.png",
	        propertiesForm: [],
		   				
	        terminals: [
	        {	name: "_INPUT",
	        	ddConfig: {
	        	      type: "input",
	        	      allowedTypes: ["output"]
	        	},
	        	wireConfig: {"drawingMethod": "arrows"},
	            direction: [-1,0], 
	            offsetPosition: {
	                left: -15, 
	                top: 9
	            }
	        },   
	        ],
		   				
	        fields: [],
	    }
	},
	
	
	]
};