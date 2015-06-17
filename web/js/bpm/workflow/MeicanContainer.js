/**
 * Container represented by an image
 * @class ImageContainer
 * @extends WireIt.Container
 * @constructor
 * @param {Object} options
 * @param {WireIt.Layer} layer
 */
WireIt.MeicanContainer = function(options, layer) {
   WireIt.MeicanContainer.superclass.constructor.call(this, options, layer);
};

YAHOO.lang.extend(WireIt.MeicanContainer, WireIt.FormContainer, {
   
   /**
    * @method setOptions
    * @param {Object} options the options object
    */
   setOptions: function(options) {
      WireIt.MeicanContainer.superclass.setOptions.call(this, options);
      
      this.options.image = options.image;
      this.options.xtype = "WireIt.MeicanContainer";
      
      this.options.className = options.className || "WireIt-Container WireIt-MeicanContainer";
      
      // Overwrite default value for options:
      this.options.resizable = (typeof options.resizable == "undefined") ? false : options.resizable;
      this.options.ddHandle = (typeof options.ddHandle == "undefined") ? false : options.ddHandle;
   },
   
   onAddWire: function(event, args) {
	   WireIt.MeicanContainer.superclass.onAddWire.call(this, event, args);
	   
	   var wire = args[0];
	   if(wire.terminal1.el.title == "_INPUT" && wire.terminal2.el){
		   wire.terminal1.el.title = "_TEMP";
	   }
	   else if(wire.terminal1.el.title == "_TEMP"){
		   wire.terminal1.el.title = "_INPUT";
		   var aux = wire.terminal2;
		   wire.terminal2 = wire.terminal1;
		   wire.terminal1 = aux;
	   }
   },
   
   /**
    * @method render
    */
   render: function() {
      WireIt.MeicanContainer.superclass.render.call(this);
      YAHOO.util.Dom.setStyle(this.bodyEl, "background-image", "url("+this.options.image+")");
   },

});