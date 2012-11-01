var meicanPolicyLanguage = {
	
	// Set a unique name for the language
	languageName: "policyLanguage",

	// inputEx fields for pipes properties
	propertiesFields: [
		// default fields (the "name" field is required by the WiringEditor):
		{"type": "string", inputParams: {"name": "name", label: "Title", typeInvite: "Enter a policy name" } },
		{"type": "text", inputParams: {"name": "description", label: "Description", typeInvite: "Describe about our policy", cols: 55} },
		
		// Additional fields
		{"type": "boolean", inputParams: {"name": "isTest", value: true, label: "Test"}},
		{"type": "select", inputParams: {"name": "category", label: "Category", selectValues: ["Demo", "Test", "Other"]} }
	],
	
	// List of node types definition
	modules: [
	/*
	{
	      "name": "Bandwidth_FormContainer",
	      "container": {
	   		"xtype": "WireIt.FormContainer",
	   		"title": "WireIt.FormContainer demo",    
	   		"icon": "../../res/icons/application_edit.png",

	   		"collapsible": true,
	   		"fields": [ 
	   			{"type": "select", "inputParams": {"label": "", "operator": "title", "selectValues": ["<","<=",">",">=","=="] } },
	   			{"inputParams": {"label": "", "name": "badwidth", "required": true }} 
	   			//{"inputParams": {"label": "Lastname", "name": "lastname", "value":"Dupont"} }, 
	   			//{"type":"email", "inputParams": {"label": "Email", "name": "email", "required": true, "wirable": true}}, 
	   			//{"type":"boolean", "inputParams": {"label": "Happy to be there ?", "name": "happy"}}, 
	   			//{"type":"url", "inputParams": {"label": "Website", "name":"website", "size": 25}} 
	   		],
	   		"legend": "How much Bandwidth?"
	   	}
	   },
	   
	   {
	      "name": "FormContainer",
	      "container": {
	   		"xtype": "WireIt.FormContainer",
	   		"title": "WireIt.FormContainer demo",    
	   		"icon": "../../res/icons/application_edit.png",

	   		"collapsible": true,
	   		"fields": [ 
	   			{"type": "select", "inputParams": {"label": "Title", "name": "title", "selectValues": ["Mr","Mrs","Mme"] } },
	   			{"inputParams": {"label": "Firstname", "name": "firstname", "required": true } }, 
	   			{"inputParams": {"label": "Lastname", "name": "lastname", "value":"Dupont"} }, 
	   			{"type":"email", "inputParams": {"label": "Email", "name": "email", "required": true, "wirable": true}}, 
	   			{"type":"boolean", "inputParams": {"label": "Happy to be there ?", "name": "happy"}}, 
	   			{"type":"url", "inputParams": {"label": "Website", "name":"website", "size": 25}} 
	   		],
	   		"legend": "Tell us about yourself..."
	   	}
	   },
	
		{
	      "name": "comment",
	
	      "container": {
	         "xtype": "WireIt.FormContainer",
				"icon": "../../res/icons/comment.png",
	   		"title": "Comment",
	   		"fields": [
	            {"type": "text", "inputParams": {"label": "", "name": "comment", "wirable": false }}
	         ]
	      },
	      "value": {
	         "input": {
	            "type":"url","inputParams":{}
	         }
	      }
	   },
	   */
		{
	         "name": "Start Policy",
	         "container": {
	      		"xtype":"WireIt.ImageContainer", 
	      		"image": "request.png",
	      		//"icon": "imagem_teste.png",
	      		"terminals": [
	      			{"name": "_INPUT1", "direction": [-1,0], "offsetPosition": {"left": -15, "top": 20 }},
	      			
	      			//{"name": "_INPUT2", "direction": [-1,0], "offsetPosition": {"left": -3, "top": 37 }},
	      			{"name": "_OUTPUT", "direction": [1,0], "offsetPosition": {"left": 55, "top": 20 }}
	      		]
	      	}
	      },
	      {
	         "name": "Filter for Domain",
	         "container": {
	      		"xtype":"WireIt.ImageContainer", 
	      		"image": "domain.png",
	      		//"icon": "imagem_teste.png",
	      		"terminals": [
	      			{"name": "_INPUT1", "direction": [-1,0], "offsetPosition": {"left": -15, "top": 20 }},
	      			//{"name": "_INPUT2", "direction": [-1,0], "offsetPosition": {"left": -3, "top": 37 }},
	      			{"name": "_OUTPUT", "direction": [1,0], "offsetPosition": {"left": 55, "top": 20 }}
	      		]
	      	}
	      },
	      {
	         "name": "Filter for Human",
	         "container": {
	      		"xtype":"WireIt.ImageContainer", 
	      		"image": "user.png",
	      		//"icon": "imagem_teste.png",
	      		"terminals": [
	      			{"name": "_INPUT1", "direction": [-1,0], "offsetPosition": {"left": -15, "top": 20 }},
	      			//{"name": "_INPUT2", "direction": [-1,0], "offsetPosition": {"left": -3, "top": 37 }},
	      			{"name": "_OUTPUT", "direction": [1,0], "offsetPosition": {"left": 55, "top": 20 }}
	      		]
	      	}
	      },
	      
	      {
	         "name": "Filter for Bandwidth",
	         "container": {
	      		"xtype":"WireIt.ImageContainer", 
	      		"image": "bandwidth.png",
	      		//"icon": "imagem_teste.png",
	      		"terminals": [
	      			{"name": "_INPUT1", "direction": [-1,0], "offsetPosition": {"left": -15, "top": 20 }},
	      			//{"name": "_INPUT2", "direction": [-1,0], "offsetPosition": {"left": -3, "top": 37 }},
	      			{"name": "_OUTPUT", "direction": [1,0], "offsetPosition": {"left": 55, "top": 20 }}
	      		]
	      	}
	      },
	      
	      
	      {
	         "name": "Filter for Time",
	         "container": {
	      		"xtype":"WireIt.ImageContainer", 
	      		"image": "time.png",
	      		//"icon": "imagem_teste.png",
	      		"terminals": [
	      			{"name": "_INPUT1", "direction": [-1,0], "offsetPosition": {"left": -15, "top": 20 }},
	      			//{"name": "_INPUT2", "direction": [-1,0], "offsetPosition": {"left": -3, "top": 37 }},
	      			{"name": "_OUTPUT", "direction": [1,0], "offsetPosition": {"left": 55, "top": 20 }}
	      		]
	      	}
	      },
	      
	      {
	         "name": "Request Human Authorization",
	         "container": {
	      		"xtype":"WireIt.ImageContainer", 
	      		"image": "request_user.png",
	      		//"icon": "imagem_teste.png",
	      		"terminals": [
	      			{"name": "_INPUT1", "direction": [-1,0], "offsetPosition": {"left": -15, "top": 20 }},
	      			//{"name": "_INPUT2", "direction": [-1,0], "offsetPosition": {"left": -3, "top": 37 }},
	      			{"name": "_OUTPUT", "direction": [1,0], "offsetPosition": {"left": 80, "top": -2}},
	      			{"name": "_OUTPUT2", "direction": [1,1], "offsetPosition": {"left": 80, "top": 37 }}
	      		]
	      	}
	      },
	       {
	         "name": "Request Group Authorization",
	         "container": {
	      		"xtype":"WireIt.ImageContainer", 
	      		"image": "request_group.png",
	      		//"icon": "imagem_teste.png",
	      		"terminals": [
	      			{"name": "_INPUT1", "direction": [-1,0], "offsetPosition": {"left": -15, "top": 20 }},
	      			//{"name": "_INPUT2", "direction": [-1,0], "offsetPosition": {"left": -3, "top": 37 }},
	      			{"name": "_OUTPUT", "direction": [1,0], "offsetPosition": {"left": 80, "top": -2}},
	      			{"name": "_OUTPUT2", "direction": [1,1], "offsetPosition": {"left": 80, "top": 37 }}
	      		]
	      	}
	      },
	      
	      {
	         "name": "Notify Human",
	         "container": {
	      		"xtype":"WireIt.ImageContainer", 
	      		"image": "notify_user.png",
	      		//"icon": "imagem_teste.png",
	      		"terminals": [
	      			{"name": "_INPUT1", "direction": [-1,0], "offsetPosition": {"left": -15, "top": 20 }},
	      			//{"name": "_INPUT2", "direction": [-1,0], "offsetPosition": {"left": -3, "top": 37 }},
	      			{"name": "_OUTPUT", "direction": [1,0], "offsetPosition": {"left": 55, "top": 20 }}
	      		],
	      	}
	      },
	      
	      {
	         "name": "Notify Group",
	         "container": {
	      		"xtype":"WireIt.ImageContainer", 
	      		"image": "notify_user.png",
	      		//"icon": "imagem_teste.png",
	      		"terminals": [
	      			{"name": "_INPUT1", "direction": [-1,0], "offsetPosition": {"left": -15, "top": 20 }},
	      			//{"name": "_INPUT2", "direction": [-1,0], "offsetPosition": {"left": -3, "top": 37 }},
	      			{"name": "_OUTPUT", "direction": [1,0], "offsetPosition": {"left": 55, "top": 20 }}
	      		],
	      	}
	      },
	      
	      {
	         "name": "Accept Automatically",
	         "container": {
	      		"xtype":"WireIt.ImageContainer", 
	      		"image": "accept.png",
	      		//"icon": "imagem_teste.png",
	      		"terminals": [
	      			{"name": "_INPUT1", "direction": [-1,0], "offsetPosition": {"left": -15, "top": 20 }},
	      			//{"name": "_INPUT2", "direction": [-1,0], "offsetPosition": {"left": -3, "top": 37 }},
	      			{"name": "_OUTPUT", "direction": [1,0], "offsetPosition": {"left": 55, "top": 20 }}
	      		],
	      	}
	      },
	      {
	         "name": "Deny Automatically",
	         "container": {
	      		"xtype":"WireIt.ImageContainer", 
	      		"image": "deny.png",
	      		//"icon": "imagem_teste.png",
	      		"terminals": [
	      			{"name": "_INPUT1", "direction": [-1,0], "offsetPosition": {"left": -15, "top": 20 }},
	      			//{"name": "_INPUT2", "direction": [-1,0], "offsetPosition": {"left": -3, "top": 37 }},
	      			{"name": "_OUTPUT", "direction": [1,0], "offsetPosition": {"left": 55, "top": 20 }}
	      		]
	      	}
	      },
	      
	      /*
	      {
	         "name": "AND gate",
	         "container": {
	      		"xtype":"WireIt.ImageContainer", 
	      		"image": "../logicGates/images/gate_and.png",
	      		"icon": "../../res/icons/arrow_join.png",
	      		"terminals": [
	      			{"name": "_INPUT1", "direction": [-1,0], "offsetPosition": {"left": -3, "top": 2 }},
	      			{"name": "_INPUT2", "direction": [-1,0], "offsetPosition": {"left": -3, "top": 37 }},
	      			{"name": "_OUTPUT", "direction": [1,0], "offsetPosition": {"left": 103, "top": 20 }}
	      		]
	      	}
	      },
	      


				{
					"name": "Bubble",
					"container": {
	         		"xtype":"WireIt.ImageContainer", 
	         		"className": "WireIt-Container WireIt-ImageContainer Bubble",
	            	"icon": "../../res/icons/color_wheel.png",
	         		"image": "../images/bubble.png",
	         		"terminals": [
	         				{"direction": [-1,-1], "offsetPosition": {"left": -10, "top": -10 }, "name": "tl"},
	         				{"direction": [1,-1], "offsetPosition": {"left": 25, "top": -10 }, "name": "tr"},
	         				{"direction": [-1,1], "offsetPosition": {"left": -10, "top": 25 }, "name": "bl"},
	         				{"direction": [1,1], "offsetPosition": {"left": 25, "top": 25 }, "name": "br"}
	         		]
	         	}
		      },

				{
					"name": "Other form module",
					"container": {
	   				"icon": "../../res/icons/application_edit.png",
	   				"xtype": "WireIt.FormContainer",
	   				"outputTerminals": [],
	   				"propertiesForm": [],
	   				"fields": [ 
	   					{"type": "select", "inputParams": {"label": "Title", "name": "title", "selectValues": ["Mr","Mrs","Mme"] } },
	   					{"inputParams": {"label": "Firstname", "name": "firstname", "required": true } }, 
	   					{"inputParams": {"label": "Lastname", "name": "lastname", "value":"Dupont"} }, 
	   					{"type":"email", "inputParams": {"label": "Email", "name": "email", "required": true}}, 
	   					{"type":"boolean", "inputParams": {"label": "Happy to be there ?", "name": "happy"}}, 
	   					{"type":"url", "inputParams": {"label": "Website", "name":"website", "size": 25}} 
	   				]
					}
				},
				"collapsible": true,
	   		"fields": [ 
	   			{"type": "select", "inputParams": {"label": "", "operator": "title", "selectValues": ["<","<=",">",">=","=="] } },
	   			{"inputParams": {"label": "", "name": "badwidth", "required": true }} 
	   			//{"inputParams": {"label": "Lastname", "name": "lastname", "value":"Dupont"} }, 
	   			//{"type":"email", "inputParams": {"label": "Email", "name": "email", "required": true, "wirable": true}}, 
	   			//{"type":"boolean", "inputParams": {"label": "Happy to be there ?", "name": "happy"}}, 
	   			//{"type":"url", "inputParams": {"label": "Website", "name":"website", "size": 25}} 
	   		],
	   		"legend": "How much Bandwidth?"
							
				{
					"name": "PostContainer",
					"container": {
					"xtype": "WireIt.FormContainer",
					"title": "Post",
					"icon": "../../res/icons/comments.png",
				
					"fields": [
					{"type": "inplaceedit", "inputParams": {
					"name": "post",
					"editorField":{"type":"text", "inputParams": {"label": "jair"} },
					"animColors":{"from":"#FFFF99" , "to":"#DDDDFF"}
					}},
			
					{"type": "list", "inputParams": {
					"label": "Comments", "name": "comments", "wirable": false,
					"elementType": {"type":"string", "inputParams": { "wirable": false } }
					}
					}
			
					],
			
					"terminals":[{
						"name" : "SOURCES",
						"direction" : [0, -1],
						"offsetPosition" : {
							"left" : 100,
							"top" : -15
						}
					}, {
						"name" : "FOLLOWUPS",
						"direction" : [0, 1],
						"offsetPosition" : {
							"left" : 100,
							"bottom" : -15
						}
					}]
				}
				},

	
	
				{
		         "name": "InOut test",
		         "container": {
		      		"xtype":"WireIt.InOutContainer", 
		      		"icon": "../../res/icons/arrow_right.png",
						"inputs": ["text1", "text2", "option1"],
						"outputs": ["result", "error"]
		      	}
		      }
			*/	
			]

};
