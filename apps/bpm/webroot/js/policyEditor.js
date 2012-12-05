var imagePath = baseUrl + 'apps/bpm/webroot/img/';
var iconPath = baseUrl + 'apps/bpm/webroot/img/' + parent.language + '/';

var domains = parent.domains ? parent.domains : [];
var users = parent.users ? parent.users : [];
var groups = parent.groups ? parent.groups : [];

var meicanPolicyLanguage = {
	
    // Set a unique name for the language
    languageName: "meicanPolicyLanguage",
    accordionViewParams: {
    //width: 0
	
    },
    layoutOptions: {
        units: [
        {
            position: 'top', 
            height: 50, 
            body: 'top'
        },

        {
            position: 'right', 
            width: 200, 
            resize: true, 
            body: 'left', 
            gutter: '5px', 
            collapse: true, 
            collapseSize: 25, 
            scroll: false, 
            animate: true
        },

        {
            position: 'center', 
            body: 'center', 
            gutter: '5px'
        }
        ]
    },
    layerOptions: {
        layerMap: false
    },
    // inputEx fields for pipes properties
    propertiesFields: [
    // default fields (the "name" field is required by the WiringEditor):
    {
        "type": "string", 
        inputParams: {
            "name": "name", 
            label: parent.string_workflow_name + ":",
            typeInvite: parent.string_enter_title,
            cols: 5
        }
    },
    {
    	"type": "select", 
        "inputParams": {
		"name": "domains_owner",                 
		"label": parent.string_workflow_name + ": aa ", 
                "selectValues": domains
         }
    }
    

],
	
// List of node types definition
			
modules: [
{
    "name": "New_Request",
    "container": {
        "icon": iconPath + "ico_request.png",
        "xtype": "WireIt.MeicanContainer",
        "image": imagePath + "request.png",
        "propertiesForm": [],
	   				
        "terminals": [
        {
            "name": "_OUTPUT", 
            "direction": [1,0], 
            "offsetPosition": {
                "left": 55, 
                "top": 20
            }
        }
    ],
	   				
    "fields": []
}
},

{
    "name": "Domain",
    "container": {
        "icon": iconPath + "ico_domain.png",
        "xtype": "WireIt.MeicanContainer",
        "image": imagePath + "domain.png",
        "propertiesForm": [],
	   				
        "terminals": [
        {
            "name": "_INPUT1", 
            "direction": [-1,0], 
            "offsetPosition": {
                "left": -15, 
                "top": 20
            }
        },

        {
        "name": "_OUTPUT", 
        "direction": [1,0], 
        "offsetPosition": {
            "left": 55, 
            "top": 20
        }
    }
],
	   				
"fields": [
{
    "type": "inplaceedit", 
    "inputParams": {
        "name": "post",
        "editorField":{
            "type": "select", 
            "inputParams": {
                "label": "", 
                "name": "title", 
                "selectValues": domains
                }
            },
    "animColors":{
        "from":"#FFFF99" , 
        "to":"#DDDDFF"
    }
}
}, 
					
]
}
},
{
    "name": "User",
    "container": {
        "icon": iconPath + "ico_user.png",
        "xtype": "WireIt.MeicanContainer",
        "image": imagePath + "user.png",
        "propertiesForm": [],
	   				
        "terminals": [
        {
            "name": "_INPUT1", 
            "direction": [-1,0], 
            "offsetPosition": {
                "left": -15, 
                "top": 20
            }
        },

        {
        "name": "_OUTPUT", 
        "direction": [1,0], 
        "offsetPosition": {
            "left": 55, 
            "top": 20
        }
    }
],
	   				
"fields": [
{
    "type": "inplaceedit", 
    "inputParams": {
        "name": "post",
        "editorField":{
            "type": "select", 
            "inputParams": 

            {
                "label": "", 
                "name": "title", 
                "selectValues": users
                }
            },
    "animColors":{
        "from":"#FFFF99" , 
        "to":"#DDDDFF"
    }
}
}, 
					
				
]
}
},
	
{
    "name": "Bandwidth",
    "container": {
        "icon": iconPath + "ico_bandwidth.png",
        "xtype": "WireIt.MeicanContainer",
        "image": imagePath + "bandwidth.png",
        "propertiesForm": [],
		
        "terminals": [
        {
            "name": "_INPUT1", 
            "direction": [-1,0], 
            "offsetPosition": {
                "left": -15, 
                "top": 20
            }
        },

        {
        "name": "_OUTPUT", 
        "direction": [1,0], 
        "offsetPosition": {
            "left": 55, 
            "top": 20
        }
    }
],
		
"fields": [{
    "type": "inplaceedit", 
    "inputParams": {
        "name": "post",
        editorField: {
            type: "group", 
            inputParams: {
                fields:[
					

                {
                    "type": "select", 
                    "inputParams": {
                        "name": "operator", 
                        "selectValues": ["== ","< ","<= ","> ",">= "]
                    }
                },

                {
                "inputParams": {
                    "name": "bandwidth", 
                    "required": true
                }
            }
        ]
        }
},
visu: {
    visuType: 'func', 
    func: function(val) {
        console.debug(val);
        return val.operator + val.bandwidth;
    }
},
animColors:{
    "from":"#FFFF99" , 
    "to":"#DDDDFF"
},
//value: {
//  title: 'Lena',
//title1: 'Idontknow'
//}
}
}]
}
},	
{
    "name": "Request_User_Authorization",
    "container": {
        "xtype":"WireIt.MeicanContainer", 
        "image": imagePath + "request_user.png",
        "icon": iconPath + "ico_request_user.png",

        "terminals": [
        {
            "name": "_INPUT1", 
            "direction": [-1,0], 
            "offsetPosition": {
                "left": -15, 
                "top": 20
            }
        },
	      			

        {
        "name": "_OUTPUT", 
        "direction": [1,0], 
        "offsetPosition": {
            "left": 80, 
            "top": -2
        }
    },
{
    "name": "_OUTPUT2", 
    "direction": [1,1], 
    "offsetPosition": {
        "left": 80, 
        "top": 37
    }
}
],
"fields": [
{
    "type": "inplaceedit", 
    "inputParams": {
        "name": "post",
        "editorField":{
            "type": "select", 
            "inputParams": 

            {
                "label": "", 
                "name": "title", 
                "selectValues": users
                }
            },
    "animColors":{
        "from":"#FFFF99" , 
        "to":"#DDDDFF"
    }
}
}, 
					
				
],
}
},
{
    "name": "Request_Group_Authorization",
    "container": {
        "xtype":"WireIt.MeicanContainer", 
        "image": imagePath + "request_group.png",
        "icon": iconPath + "ico_request_group.png",

        "terminals": [
        {
            "name": "_INPUT1", 
            "direction": [-1,0], 
            "offsetPosition": {
                "left": -15, 
                "top": 20
            }
        },
	      			

        {
        "name": "_OUTPUT", 
        "direction": [1,0], 
        "offsetPosition": {
            "left": 80, 
            "top": -2
        }
    },
{
    "name": "_OUTPUT2", 
    "direction": [1,1], 
    "offsetPosition": {
        "left": 80, 
        "top": 37
    }
}
],
"fields": [
{
    "type": "inplaceedit", 
    "inputParams": {
        "name": "post",
        "editorField":{
            "type": "select", 
            "inputParams": 

            {
                "label": "", 
                "name": "title", 
                "selectValues": groups
                }
            },
    "animColors":{
        "from":"#FFFF99" , 
        "to":"#DDDDFF"
    }
}
}, 
					
				
],
}
},
{
    "name": "Accept_Automatically",
    "container": {
        "icon": iconPath + "ico_accept.png",
        "xtype": "WireIt.MeicanContainer",
        "image": imagePath + "accept.png",
        "propertiesForm": [],
	   				
        "terminals": [
        {
            "name": "_INPUT1", 
            "direction": [-1,0], 
            "offsetPosition": {
                "left": -15, 
                "top": 20
            }
        }
],
	   				
"fields": [
],
}
},
	      
{
    "name": "Deny_Automatically",
    "container": {
        "icon": iconPath + "ico_deny.png",
        "xtype": "WireIt.MeicanContainer",
        "image": imagePath + "deny.png",
        "propertiesForm": [],
	   				
        "terminals": [
        {
            "name": "_INPUT1", 
            "direction": [-1,0], 
            "offsetPosition": {
                "left": -15, 
                "top": 20
            }
        },

       
],
	   				
"fields": [
],
}
},
	      
	      
	      
]

};
