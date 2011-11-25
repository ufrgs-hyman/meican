/*
 *
 * Copyright (c) 2006-2010 Sam Collett (http://www.texotela.co.uk)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 * 
 * Version 1.2
 * Demo: http://www.texotela.co.uk/code/jquery/numeric/
 *
 */
(function($) {
/*
 * Allows only valid characters to be entered into input boxes.
 * Note: does not validate that the final text is a valid number
 * (that could be done by another script, or server-side)
 *
 * @name     numeric
 * @param    decimal      Decimal separator (e.g. '.' or ',' - default is '.'). Pass false for integers
 * @param    callback     A function that runs if the number is not valid (fires onblur)
 * @author   Sam Collett (http://www.texotela.co.uk)
 * @example  $(".numeric").numeric();
 * @example  $(".numeric").numeric(",");
 * @example  $(".numeric").numeric(null, callback);
 *
 */
$.fn.numeric = function(decimal, callback)
{
	decimal = (decimal === false) ? "" : decimal || ".";
	callback = typeof callback == "function" ? callback : function(){};
	return this.data("numeric.decimal", decimal).data("numeric.callback", callback).keypress($.fn.numeric.keypress).blur($.fn.numeric.blur);
}

$.fn.numeric.keypress = function(e)
{
	var decimal = $.data(this, "numeric.decimal");
	var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
	// allow enter/return key (only when in an input box)
	if(key == 13 && this.nodeName.toLowerCase() == "input")
	{
		return true;
	}
	else if(key == 13)
	{
		return false;
	}
	var allow = false;
	// allow Ctrl+A
	if((e.ctrlKey && key == 97 /* firefox */) || (e.ctrlKey && key == 65) /* opera */) return true;
	// allow Ctrl+X (cut)
	if((e.ctrlKey && key == 120 /* firefox */) || (e.ctrlKey && key == 88) /* opera */) return true;
	// allow Ctrl+C (copy)
	if((e.ctrlKey && key == 99 /* firefox */) || (e.ctrlKey && key == 67) /* opera */) return true;
	// allow Ctrl+Z (undo)
	if((e.ctrlKey && key == 122 /* firefox */) || (e.ctrlKey && key == 90) /* opera */) return true;
	// allow or deny Ctrl+V (paste), Shift+Ins
	if((e.ctrlKey && key == 118 /* firefox */) || (e.ctrlKey && key == 86) /* opera */
	|| (e.shiftKey && key == 45)) return true;
	// if a number was not pressed
	if(key < 48 || key > 57)
	{
		/* '-' only allowed at start */
		if(key == 45 && this.value.length == 0) return true;
		/* only one decimal separator allowed */
		if(decimal && key == decimal.charCodeAt(0) && this.value.indexOf(decimal) != -1)
		{
			allow = false;
		}
		// check for other keys that have special purposes
		if(
			key != 8 /* backspace */ &&
			key != 9 /* tab */ &&
			key != 13 /* enter */ &&
			key != 35 /* end */ &&
			key != 36 /* home */ &&
			key != 37 /* left */ &&
			key != 39 /* right */ &&
			key != 46 /* del */
		)
		{
			allow = false;
		}
		else
		{
			// for detecting special keys (listed above)
			// IE does not support 'charCode' and ignores them in keypress anyway
			if(typeof e.charCode != "undefined")
			{
				// special keys have 'keyCode' and 'which' the same (e.g. backspace)
				if(e.keyCode == e.which && e.which != 0)
				{
					allow = true;
					// . and delete share the same code, don't allow . (will be set to true later if it is the decimal point)
					if(e.which == 46) allow = false;
				}
				// or keyCode != 0 and 'charCode'/'which' = 0
				else if(e.keyCode != 0 && e.charCode == 0 && e.which == 0)
				{
					allow = true;
				}
			}
		}
		// if key pressed is the decimal and it is not already in the field
		if(decimal && key == decimal.charCodeAt(0))
		{
			if(this.value.indexOf(decimal) == -1)
			{
				allow = true;
			}
			else
			{
				allow = false;
			}
		}
	}
	else
	{
		allow = true;
	}
	return allow;
}

$.fn.numeric.blur = function()
{
	var decimal = $.data(this, "numeric.decimal");
	var callback = $.data(this, "numeric.callback");
	var val = $(this).val();
	if(val != "")
	{
		var re = new RegExp("^\\d+$|\\d*" + decimal + "\\d+");
		if(!re.exec(val))
		{
			callback.apply(this);
		}
	}
}

$.fn.removeNumeric = function()
{
	return this.data("numeric.decimal", null).data("numeric.callback", null).unbind("keypress", $.fn.numeric.keypress).unbind("blur", $.fn.numeric.blur);
}

})(jQuery);
/*
 * jQuery UI Spinner @VERSION
 *
 * Copyright 2010, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Spinner
 *
 * Depends:
 *  jquery.ui.core.js
 *  jquery.ui.widget.js
 */
(function($) {

$.widget('ui.spinner', {
	options: {
		incremental: true,
		max: null,
		min: null,
		numberformat: null,
		page: 10,
		step: null,
		value: null
	},
	
	_create: function() {
		this._draw();
		this._markupOptions();
		this._mousewheel();
		this._aria();
	},
	
	_markupOptions: function() {
		var _this = this;
		$.each({
			min: -Number.MAX_VALUE,
			max: Number.MAX_VALUE,
			step: 1
		}, function(attr, defaultValue) {
			if (_this.options[attr] === null) {
				var value = _this.element.attr(attr);
				_this.options[attr] = typeof value == "string" && value.length > 0 ? _this._parse(value) : defaultValue;
			}
		});
		this.value(this.options.value !== null ? this.options.value : this.element.val());
	},
	
	_draw: function() {
		var self = this,
			options = self.options;

		var uiSpinner = this.uiSpinner = self.element
			.addClass('ui-spinner-input')
			.attr('autocomplete', 'off')
			.wrap(self._uiSpinnerHtml())
			.parent()
				// add buttons
				.append(self._buttonHtml())
				// add behaviours
				.hover(function() {
					if (!options.disabled) {
						$(this).addClass('ui-state-hover');
					}
					self.hovered = true;
				}, function() {
					$(this).removeClass('ui-state-hover');
					self.hovered = false;
				});

		this.element
			.bind('keydown.spinner', function(event) {
				if (self.options.disabled) {
					return;
				}
				if (self._start(event)) {
					return self._keydown(event);
				}
				return true;
			})
			.bind('keyup.spinner', function(event) {
				if (self.options.disabled) {
					return;
				}
				if (self.spinning) {
					self._stop(event);
					self._change(event);					
				}
			})
			.bind('focus.spinner', function() {
				uiSpinner.addClass('ui-state-active');
				self.focused = true;
			})
			.bind('blur.spinner', function(event) {
				self.value(self.element.val());
				if (!self.hovered) {
					uiSpinner.removeClass('ui-state-active');
				}		
				self.focused = false;
			});

		// button bindings
		this.buttons = uiSpinner.find('.ui-spinner-button')
			.attr("tabIndex", -1)
			.button()
			.removeClass("ui-corner-all")
			.bind('mousedown', function(event) {
				if (self.options.disabled) {
					return;
				}
				if (self._start(event) === false) {
					return false;
				}
				self._repeat(null, $(this).hasClass('ui-spinner-up') ? 1 : -1, event);
			})
			.bind('mouseup', function(event) {
				if (self.options.disabled) {
					return;
				}
				if (self.spinning) {
					self._stop(event);
					self._change(event);					
				}
			})
			.bind("mouseenter", function() {
				if (self.options.disabled) {
					return;
				}
				// button will add ui-state-active if mouse was down while mouseleave and kept down
				if ($(this).hasClass("ui-state-active")) {
					if (self._start(event) === false) {
						return false;
					}
					self._repeat(null, $(this).hasClass('ui-spinner-up') ? 1 : -1, event);
				}
			})
			.bind("mouseleave", function() {
				if (self.spinning) {
					self._stop(event);
					self._change(event);
				}
			});
					
		// disable spinner if element was already disabled
		if (options.disabled) {
			this.disable();
		}
	},
	
	_keydown: function(event) {
		var o = this.options,
			KEYS = $.ui.keyCode;

		switch (event.keyCode) {
		case KEYS.UP:
			this._repeat(null, 1, event);
			return false;
		case KEYS.DOWN:
			this._repeat(null, -1, event);
			return false;
		case KEYS.PAGE_UP:
			this._repeat(null, this.options.page, event);
			return false;
		case KEYS.PAGE_DOWN:
			this._repeat(null, -this.options.page, event);
			return false;
			
		case KEYS.ENTER:
			this.value(this.element.val());
		}
		
		return true;
	},
	
	_mousewheel: function() {
		// need the delta normalization that mousewheel plugin provides
		if (!$.fn.mousewheel) {
			return;
		}
		var self = this;
		this.element.bind("mousewheel.spinner", function(event, delta) {
			if (self.options.disabled) {
				return;
			}
			if (!self.spinning && !self._start(event)) {
				return false;
			}
			self._spin((delta > 0 ? 1 : -1) * self.options.step, event);
			clearTimeout(self.timeout);
			self.timeout = setTimeout(function() {
				if (self.spinning) {
					self._stop(event);
					self._change(event);
				}
			}, 100);
			event.preventDefault();
		});
	},
	
	_uiSpinnerHtml: function() {
		return '<span role="spinbutton" class="ui-spinner ui-state-default ui-widget ui-widget-content ui-corner-all"></span>';
	},
	
	_buttonHtml: function() {
		return '<a class="ui-spinner-button ui-spinner-up ui-corner-tr"><span class="ui-icon ui-icon-triangle-1-n">&#9650;</span></a>' +
				'<a class="ui-spinner-button ui-spinner-down ui-corner-br"><span class="ui-icon ui-icon-triangle-1-s">&#9660;</span></a>';
	},
	
	_start: function(event) {
		if (!this.spinning && this._trigger('start', event) !== false) {
			if (!this.counter) {
				this.counter = 1;
			}
			this.spinning = true;
			return true;
		}
		return false;
	},
	
	_repeat: function(i, steps, event) {
		var self = this;
		i = i || 500;

		clearTimeout(this.timer);
		this.timer = setTimeout(function() {
			self._repeat(40, steps, event);
		}, i);
		
		self._spin(steps * self.options.step, event);
	},
	
	_spin: function(step, event) {
		if (!this.counter) {
			this.counter = 1;
		}
		
		// TODO refactor, maybe figure out some non-linear math
		var newVal = this.value() + step * (this.options.incremental &&
			this.counter > 20
				? this.counter > 100
					? this.counter > 200
						? 100 
						: 10
					: 2
				: 1);
		
		if (this._trigger('spin', event, { value: newVal }) !== false) {
			this.value(newVal);
			this.counter++;			
		}
	},
	
	_stop: function(event) {
		this.counter = 0;
		if (this.timer) {
			window.clearTimeout(this.timer);
		}
		this.element[0].focus();
		this.spinning = false;
		this._trigger('stop', event);
	},
	
	_change: function(event) {
		this._trigger('change', event);
	},
	
	_setOption: function(key, value) {
		if (key == 'value') {
			value = this._parse(value);
			if (value < this.options.min) {
				value = this.options.min;
			}
			if (value > this.options.max) {
				value = this.options.max;
			}
		}
		if (key == 'disabled') {
			if (value) {
				this.element.attr("disabled", true);
				this.buttons.button("disable");
			} else {
				this.element.removeAttr("disabled");
				this.buttons.button("enable");
			}
		}
		$.Widget.prototype._setOption.call( this, key, value );
	},
	
	_setOptions: function( options ) {
		$.Widget.prototype._setOptions.call( this, options );
		if ( "value" in options ) {
			this._format( this.options.value );
		}
		this._aria();
	},
	
	_aria: function() {
		this.element
			.attr('aria-valuemin', this.options.min)
			.attr('aria-valuemax', this.options.max)
			.attr('aria-valuenow', this.options.value);
	},
	
	_parse: function(val) {
		var input = val;
		if (typeof val == 'string') {
			// special case for currency formatting until Globalization handles currencies
			if (this.options.numberformat == "C" && window.Globalization) {
				// parseFloat should accept number format, including currency
				var culture = Globalization.culture || Globalization.cultures['default'];
				val = val.replace(culture.numberFormat.currency.symbol, "");
			}
			val = window.Globalization && this.options.numberformat ? Globalization.parseFloat(val) : +val;
		}
		return isNaN(val) ? null : val;
	},
	
	_format: function(num) {
		var num = this.options.value;
		this.element.val( window.Globalization && this.options.numberformat ? Globalization.format(num, this.options.numberformat) : num );
	},
		
	destroy: function() {
		this.element
			.removeClass('ui-spinner-input')
			.removeAttr('disabled')
			.removeAttr('autocomplete');
		$.Widget.prototype.destroy.call( this );
		this.uiSpinner.replaceWith(this.element);
	},
	
	stepUp: function(steps) {
		this._spin((steps || 1) * this.options.step);
	},
	
	stepDown: function(steps) {
		this._spin((steps || 1) * -this.options.step);	
	},
	
	pageUp: function(pages) {
		this.stepUp((pages || 1) * this.options.page);		
	},
	
	pageDown: function(pages) {
		this.stepDown((pages || 1) * this.options.page);		
	},
	
	value: function(newVal) {
		if (!arguments.length) {
			return this._parse(this.element.val());
		}
		this.option('value', newVal);
	},
	
	widget: function() {
		return this.uiSpinner;
	}
});

})(jQuery);