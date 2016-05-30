/**
The MIT License (MIT)

Copyright (c) 2013 Tobias Bieniek

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

 * @name lsidebar
 * @class L.Control.lsidebar
 * @extends L.Control
 * @param {string} id - The id of the lsidebar element (without the # character)
 * @param {Object} [options] - Optional options object
 * @param {string} [options.position=left] - Position of the lsidebar: 'left' or 'right'
 * @see L.control.lsidebar
 */
L.Control.lsidebar = L.Control.extend(/** @lends L.Control.lsidebar.prototype */ {
    includes: L.Mixin.Events,

    options: {
        position: 'left'
    },

    initialize: function (id, options) {
        var i, child;

        L.setOptions(this, options);

        // Find lsidebar HTMLElement
        this._lsidebar = L.DomUtil.get(id);

        // Attach .lsidebar-left/right class
        L.DomUtil.addClass(this._lsidebar, 'lsidebar-' + this.options.position);

        // Attach touch styling if necessary
        if (L.Browser.touch)
            L.DomUtil.addClass(this._lsidebar, 'leaflet-touch');

        // Find lsidebar > div.lsidebar-content
        for (i = this._lsidebar.children.length - 1; i >= 0; i--) {
            child = this._lsidebar.children[i];
            if (child.tagName == 'DIV' &&
                    L.DomUtil.hasClass(child, 'lsidebar-content'))
                this._container = child;
        }

        // Find lsidebar ul.lsidebar-tabs > li, lsidebar .lsidebar-tabs > ul > li
        this._tabitems = this._lsidebar.querySelectorAll('ul.lsidebar-tabs > li, .lsidebar-tabs > ul > li');
        for (i = this._tabitems.length - 1; i >= 0; i--) {
            this._tabitems[i]._lsidebar = this;
        }

        // Find lsidebar > div.lsidebar-content > div.lsidebar-pane
        this._panes = [];
        this._closeButtons = [];
        for (i = this._container.children.length - 1; i >= 0; i--) {
            child = this._container.children[i];
            if (child.tagName == 'DIV' &&
                L.DomUtil.hasClass(child, 'lsidebar-pane')) {
                this._panes.push(child);

                var closeButtons = child.querySelectorAll('.lsidebar-close');
                for (var j = 0, len = closeButtons.length; j < len; j++)
                    this._closeButtons.push(closeButtons[j]);
            }
        }
    },

    /**
     * Add this lsidebar to the specified map.
     *
     * @param {L.Map} map
     * @returns {lsidebar}
     */
    addTo: function (map) {
        var i, child;

        this._map = map;

        for (i = this._tabitems.length - 1; i >= 0; i--) {
            child = this._tabitems[i];
            L.DomEvent
                .on(child.querySelector('a'), 'click', L.DomEvent.preventDefault )
                .on(child.querySelector('a'), 'click', this._onClick, child);
        }

        for (i = this._closeButtons.length - 1; i >= 0; i--) {
            child = this._closeButtons[i];
            L.DomEvent.on(child, 'click', this._onCloseClick, this);
        }

        return this;
    },

    /**
     * Remove this lsidebar from the map.
     *
     * @param {L.Map} map
     * @returns {lsidebar}
     */
    removeFrom: function (map) {
        var i, child;

        this._map = null;

        for (i = this._tabitems.length - 1; i >= 0; i--) {
            child = this._tabitems[i];
            L.DomEvent.off(child.querySelector('a'), 'click', this._onClick);
        }

        for (i = this._closeButtons.length - 1; i >= 0; i--) {
            child = this._closeButtons[i];
            L.DomEvent.off(child, 'click', this._onCloseClick, this);
        }

        return this;
    },

    /**
     * Open lsidebar (if necessary) and show the specified tab.
     *
     * @param {string} id - The id of the tab to show (without the # character)
     */
    open: function(id) {
        var i, child;

        // hide old active contents and show new content
        for (i = this._panes.length - 1; i >= 0; i--) {
            child = this._panes[i];
            if (child.id == id)
                L.DomUtil.addClass(child, 'active');
            else if (L.DomUtil.hasClass(child, 'active'))
                L.DomUtil.removeClass(child, 'active');
        }

        // remove old active highlights and set new highlight
        for (i = this._tabitems.length - 1; i >= 0; i--) {
            child = this._tabitems[i];
            if (child.querySelector('a').hash == '#' + id)
                L.DomUtil.addClass(child, 'active');
            else if (L.DomUtil.hasClass(child, 'active'))
                L.DomUtil.removeClass(child, 'active');
        }

        this.fire('content', { id: id });

        // open lsidebar (if necessary)
        if (L.DomUtil.hasClass(this._lsidebar, 'collapsed')) {
            this.fire('opening');
            L.DomUtil.removeClass(this._lsidebar, 'collapsed');
        }

        return this;
    },

    /**
     * Close the lsidebar (if necessary).
     */
    close: function() {
        // remove old active highlights
        for (var i = this._tabitems.length - 1; i >= 0; i--) {
            var child = this._tabitems[i];
            if (L.DomUtil.hasClass(child, 'active'))
                L.DomUtil.removeClass(child, 'active');
        }

        // close lsidebar
        if (!L.DomUtil.hasClass(this._lsidebar, 'collapsed')) {
            this.fire('closing');
            L.DomUtil.addClass(this._lsidebar, 'collapsed');
        }

        return this;
    },

    /**
     * @private
     */
    _onClick: function() {
        if (L.DomUtil.hasClass(this, 'active'))
            this._lsidebar.close();
        else if (!L.DomUtil.hasClass(this, 'disabled'))
            this._lsidebar.open(this.querySelector('a').hash.slice(1));
    },

    /**
     * @private
     */
    _onCloseClick: function () {
        this.close();
    }
});

/**
 * Creates a new lsidebar.
 *
 * @example
 * var lsidebar = L.control.lsidebar('lsidebar').addTo(map);
 *
 * @param {string} id - The id of the lsidebar element (without the # character)
 * @param {Object} [options] - Optional options object
 * @param {string} [options.position=left] - Position of the lsidebar: 'left' or 'right'
 * @returns {lsidebar} A new lsidebar instance
 */
L.control.lsidebar = function (id, options) {
    return new L.Control.lsidebar(id, options);
};
