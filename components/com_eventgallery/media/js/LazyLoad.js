/*
 ---
 description:     LazyLoad

 authors:
 - David Walsh (http://davidwalsh.name)

 license:
 - MIT-style license

 requires:
 core/1.2.1:   '*'

 provides:
 - LazyLoad
 ...
 */
var LazyLoadEventgallery = new Class({

    Implements: [Options, Events],

    /* additional options */
    options: {
        range: 200,
        image: 'blank.gif',
        resetDimensions: false,
        elements: 'img',
        container: window,
        fireScroll: true, /* keeping for legacy */
        mode: 'vertical',
        startPosition: 0
    },

    /* initialize */
    initialize: function (options) {
        /* vars */
        this.setOptions(options);
        this.container = document.id(this.options.container);
        this.elements = $$(this.options.elements);
        var axis = (this.options.mode == 'vertical' ? 'y' : 'x');
        this.containerDimension = this.container.getSize()[axis];
        this.startPosition = 0;

        var offset = (this.container != window && this.container != document.body ? this.container : "");

        /* find elements remember and hold on to */

		this.elements.each(function (el) {            
            /* reset image src IF the image is below the fold and range */
            if (el.getAttribute('longDesc')) {
                el.setStyle('opacity', 0);                
            }
        }, this);
		
        this.elements = this.elements.filter(function (el) {
            var elPos = el.getPosition(offset)[axis];
            /* reset image src IF the image is below the fold and range */
            if (el.getAttribute('longDesc')) {
                
                if (elPos > this.containerDimension + this.options.range) {
                    if (this.options.resetDimensions) {
                        el.store('oWidth', el.get('width')).store('oHeight', el.get('height')).set({'width': '', 'height': ''});
                    }

                    return true;
                } else {
                    el.setStyle('background-image', 'url("' + el.getAttribute('longDesc') + '")');
                    this.fireEvent('load', [el]);
                }
            }
        }, this);
        
        /* create the action function */
        var action = function () {
            var cpos = this.container.getScroll()[axis];
            if (cpos > this.startPosition) {
                this.elements = this.elements.filter(function (el) {
                    if ((cpos + this.options.range + this.containerDimension) >= el.getPosition(offset)[axis]) {
                        if (el.getAttribute('longDesc')) {
                            //el.set('src',el.get('longDesc'));
                            el.setStyle('background-image', 'url("' + el.getAttribute('longDesc') + '")');
                        }
                        if (this.options.resetDimensions) {
                            el.set({ width: el.retrieve('oWidth'), height: el.retrieve('oHeight') });
                        }
                        this.fireEvent('load', [el]);
                        return false;
                    }
                    return true;
                }, this);
                this.startPosition = cpos;
            }
            this.fireEvent('scroll');
            /* remove this event IF no elements */
            if (!this.elements.length) {
                this.container.removeEvent('scroll', action);
                this.fireEvent('complete');
            }
        }.bind(this);

        /* listen for scroll */
        window.addEvent('scroll', action);
        if (this.options.fireScroll) {
            action();
        }
    }
});