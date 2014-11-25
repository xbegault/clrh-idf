var $defined = function (obj) {
    return (obj != undefined);
};

var JSGallery2 = new Class({
    Implements: Options,
    options: {
        'selectSpeed': 200,			//the duration of the select effect in ms (high values will make it look ugly)
        'fadeSpeed': 250,			//the duration of the image fading effect in ms
        'pageSpeed': 1000,			//the duration of the change page effect in ms
        'prevHandle': null,			//if you pass a previous page handle in here, it will be hidden if it's not needed
        'nextHandle': null,			//like above, but for next page
        'countHandle': null,		//handle of the counter variable
        'titleTarget': null,		//target HTML element where image texts are copied into
        'initialIndex': -1,			//which thumb to select after init. you could create deep links with it.
        'maxOpacity': 0.8,			//maximum opacity before cursor reaches prev/next control, then it will be set to 1 instantly.
        'next_image': 'next_image.png',
        'prev_image': 'prev_image.png',
        'showSocialMediaButton': true,
        'showCartButton': true,
        'showCartConnector': false,
		'cartConnectorLinkRel': '',
        'activeClass': 'thumbnail-active', // the css class for the active thumbnail
        'loadingClass': 'thumbnail-loading', // the css class for the loading thumbnail
        'lightboxRel': 'lightbo2' // the trigger rel for the lightbox script
    },
    /**
     *    Constructor. Starts up the whole thing :-)
     *
     *    This script is free to use. It has been created by http://www.aplusmedia.de and
     *    can be downloaded on http://www.esteak.net.
     *    License: GNU GPL 2.0: http://creativecommons.org/licenses/GPL/2.0/
     *    Example on: http://blog.aplusmedia.de/moo-gallery2
     *    Known issues:
     *    - preloading does not care about initialIndex param
     *    - hovering to a control over the border of the big image will make the other one flickering
     *    - if you enter and leave the control area very quickly, the control flickers sometimes
     *    - does not work in IE6
     *
     *    @param {Array} thumbs, An array of HTML elements
     *    @param {HTMLelement} bigImageContainer, the full size image
     *    @param {HTMLelement} pageContainer, If you have several pages, put them in this container
     *    @param {Object} options, You have to pass imagesPerPage if you have more than one!
     */
    initialize: function (thumbs, bigImageContainer, pageContainer, options) {

		var pages = pageContainer.getChildren('div');

        this.currentPageNumber = 0;
        this.loadedImages = 0;
        this.blockKeys = false;
        this.imagesPerFirstPage = pages[0].getChildren('div.thumbnail').length;
        this.imagesPerPage = this.imagesPerFirstPage;
        
        if (pages.length>1 && pages[1].getChildren('div.thumbnail').length>0)  {
            this.imagesPerPage = pages[1].getChildren('div.thumbnail').length;
        }
        
        this.setOptions(options);
        this.thumbs = thumbs;

        this.bigImage = bigImageContainer;
        this.pageContainer = pageContainer;
        this.convertThumbs();
        this.lastPage = Math.ceil((this.thumbs.length - this.imagesPerFirstPage) / this.imagesPerPage) + 1;

        var url = document.location;
        var strippedUrl = url.toString().split("#");
        this.initialIndex = 0;

        if (strippedUrl.length > 0) {
            var objRegExp = /(^\d\d*$)/;
            if (objRegExp.test(strippedUrl[1]) == true) {
                this.initialIndex = strippedUrl[1];

            }
        }

        this.createControls();

        this.gotoPage(0);

        if (this.options.initialIndex != -1) {
            this.unBlockKeys();
            this.selectByIndex(this.options.initialIndex);
        } else if (this.initialIndex != 0) {

            this.unBlockKeys();
            this.selectByIndex(this.initialIndex);
        }


        this.loadNextImage();


    },
    /**
     *    Creates the previous and next links over the big image.
     */
    createControls: function () {
        this.prevLink = new Element('a', {
            events: {
                'click': this.prevImage.bind(this),
                'mouseleave': this.mouseLeaveHandler.bind(this)
            },
            styles: {
                'width': '63px',
                'display': 'block',
                'top': '0px',
                //'border': '1px solid red',
                'position': 'absolute',

                'height': '100%',
                'background': 'url(' + this.options.prev_image + ') no-repeat 0 50%'

            },
            'href': '#',
            'class': 'link'
        }).inject(this.bigImage.getParent());

        this.prevLink.addEvent('mouseover', function (e) {
            this.focusControl(e, this.prevLink);
        }.bind(this));

        this.zoomLink = this.prevLink.clone().inject(this.prevLink, 'after').set({
            'events': {
                'click': this.zoomImage.bind(this),
                'mouseleave': this.mouseLeaveHandler.bind(this)
            },
            'styles': {
                'width': '80%',
                'left': '70px',
                'background-image': 'url(' + this.options.zoom_image + ')',
                'background-position': '50% 50%'
            },
            'rel': this.options.lightboxRel
        });

        this.zoomLink.addEvent('mouseover', function (e) {
            this.focusControl(e, this.zoomLink);
        }.bind(this));


        this.nextLink = this.prevLink.clone().inject(this.zoomLink, 'after').set({
            'events': {
                'click': this.nextImage.bind(this),
                'mouseleave': this.mouseLeaveHandler.bind(this)
            },
            'styles': {
                'right': '0px',
                'background-image': 'url(' + this.options.next_image + ')'
            }
            	
        });
        this.prevLink.setStyle('left', 0);

        this.nextLink.addEvent('mouseover', function (e) {
            this.focusControl(e, this.nextLink);
        }.bind(this));

        if (this.options.showCartButton) {

            this.add2cartLink = new Element('a', {
                href: '#',
                'class': 'eventgallery-add2cart button-add2cart btn btn-primary',
                html: '<i class="big"></i>',
                id: 'ajax-add2cartbuttoncount',
                styles: {
                    'font-size': '59px',
                    'right': '0px',
                    'z-index': 999,
                    'position': 'absolute',
                    'display': 'block',
                    'right': '10px',
                    'top': '10px'
                }
            });
			this.add2cartLink.hide();
            this.bigImage.getParent().grab(this.add2cartLink);            
            $(document.body).fireEvent('updatecartlinks');
        }

        if (this.options.showCartConnector) {

            this.cartConnectorLink = new Element('a', {
                href: '#',
                'class': 'button-cart-connector',
                html: '<i class="big"></i>',
                id: 'ajax-cartconnector',
			    rel: this.options.cartConnectorLinkRel,
                styles: {
                    'font-size': '59px',
                    'right': '0px',
                    'z-index': 999,
                    'position': 'absolute',
                    'display': 'block',
                    'right': '10px',
                    'top': '10px'
                }
            });

            this.bigImage.getParent().grab(this.cartConnectorLink);
        }
        
        if (this.options.showSocialMediaButton) {

			this.socialmediabutton = new Element('a', {
			    href: '#',
			    rel: 'nofollow',
			    'class': 'social-share-button social-share-button-open',
			    html: '<i class="big"></i>',
			    id: 'ajax-social-media-button',
			    styles: {
			    	'font-size': '59px',
			    	'right': '0px', 
			    	'z-index': 999,
			    	'position' : 'absolute',
			    	'display' : 'block',
			    	'left': '10px', 
			    	'top': '10px',
			    	'width': '40px'
			    }
			});		
			
			this.bigImage.getParent().grab(this.socialmediabutton);

		}

        this.bigImage.addEvents({
            'mousemove': this.mouseOverHandler.bind(this),
            'mouseleave': this.mouseLeaveHandler.bind(this)
        });
        document.addEvent('keydown', this.keyboardHandler.bind(this));
        this.mouseLeaveHandler();

    },
    /**
     * Focuses one control
     *
     * @param {Event} event
     * @param {HTMLElement} control
     */
    focusControl: function (event, control) {
        control.setStyle('opacity', 1);
    },
    /**
     *    Handles mouse movement over the big image.
     * @param {Event} event
     */
    mouseOverHandler: function (event) {
        var currentIndex = this.thumbs.indexOf(this.selectedContainer);
        //this makes the control on the other side fade out in just the moment when you reach one
        var activeRange = this.bigImage.getSize().x;
        var op = 0;
        if (currentIndex < this.thumbs.length - 1) {
            op = this.options.maxOpacity - this.getDistanceToMouse(this.nextLink, event) / activeRange;

        }
        this.nextLink.setStyle('opacity', op);

        op = 0;
        if (currentIndex > 0) {
            op = this.options.maxOpacity - this.getDistanceToMouse(this.prevLink, event) / activeRange;

        }
        this.prevLink.setStyle('opacity', op);

        op = 0;
        op = this.options.maxOpacity - this.getDistanceToMouse(this.zoomLink, event) / activeRange * 2;
        op = 1;
        this.zoomLink.setStyle('opacity', op);
    },
    /**
     * Hides the controls.
     */
    mouseLeaveHandler: function () {
        this.nextLink.setStyle('opacity', 0);
        this.prevLink.setStyle('opacity', 0);
        this.zoomLink.setStyle('opacity', 0);
    },
    /**
     * Handles keyboard interactions.
     * @param {Event} event
     */
    keyboardHandler: function (event) {
        if (EventGalleryMediabox && EventGalleryMediabox.isActive()
            && eventGalleryMediaBoxImages
            && eventGalleryMediaBoxImages[0][2] == 'cart') {
            return;
        }
        if (!this.blockKeys) {
            if (event.code >= 49 && event.code <= 57) {
                this.gotoPage(event.key - 1);
            } else if (event.key == "left") {
                this.prevImage(event);
            } else if (event.key == "right") {
                this.nextImage(event);
            }
        }
    },
    /**
     *    Returns the distance to the mouse from the middle of a given element.
     *    @param {HTMLelement} element
     *    @param {Event} event
     *    @return integer
     */
    getDistanceToMouse: function (element, event) {
        var s = element.getSize();
        var xDiff = Math.abs(event.client.x - (element.getLeft() + s.x / 2));
        var yDiff = Math.abs(event.client.y - (element.getTop() + s.y / 2));
        return Math.sqrt(Math.pow(xDiff, 2) + Math.pow(yDiff, 2));
    },
    resetThumbs: function () {
        this.loadedImages = 0;
        this.convertThumbs();
        this.loadNextImage();
        //if we like to select another image on that page than the first one

        this.select(this.selectedContainer, true);

    },
    /**
     *    Adds the border to the thumbs and so on. (conversion of static thumbs)
     */
    convertThumbs: function () {
        this.thumbs.each(function (thumbContainer, count) {
            this.convertThumb(thumbContainer, count);
        }, this);
    },
    /**
     * Converts one single thumb.
     * @param {HTMLelement} thumbContainer
     * @param {Integer} count
     */
    convertThumb: function (thumbContainer, count) {
        if (!$defined(thumbContainer)) {
            return;
        }

        thumbContainer.addEvent('click', this.select.bind(this, thumbContainer)).setStyle('position', 'relative').set('counter', count);
        thumbContainer.getFirst().set('href', 'javascript: void(0);')
        thumbContainer.addClass(this.options.loadingClass);

    },
    /**
     *    Removes key blocking.
     */
    unBlockKeys: function () {
        this.blockKeys = false;
    },
    /**
     *    Selects a certain image. (You have to pass the outer container of the image)
     *    @param {HTMLelement} container
     */
    select: function (container, forceReload) {
        forceReload = typeof forceReload !== 'undefined' ? forceReload : false;

        if (this.blockKeys || !$defined(container)) {
            return false;
        }


        this.blockKeys = true;
        if ($defined(this.selectedContainer)) {
            //this prevents an ugly effect if you click on the currently selected item
            if (container == this.selectedContainer && !forceReload) {
                this.unBlockKeys();
                return false;
            }
            this.deselect(this.selectedContainer);
        }

        // handle URL
        var url = document.location;
        var strippedUrl = url.toString().split("#");
        document.location.href = strippedUrl[0] + '#' + this.thumbs.indexOf(container);

        //if target image is not on current page, we have to go there first
        var targetPage = Math.floor((container.get('counter') - this.imagesPerFirstPage) / this.imagesPerPage) + 1;

        if (this.currentPageNumber != targetPage) {
            this.gotoPage(targetPage, container);
        }
        this.selectedContainer = container;

        container.addClass(this.options.activeClass);

        //first link in the container
        var source = container.getFirst();


        // prepare the add2cart button
        if (this.options.showCartButton) {
            this.add2cartLink.set('data-id', source.getAttribute('data-id'));
            $$('.eventgallery-add2cart').set('data-id', source.getAttribute('data-id'));
        }

        if (this.options.showCartConnector) {
            this.cartConnectorLink.set('data-folder', source.getAttribute('data-folder'));
            this.cartConnectorLink.set('data-file', source.getAttribute('data-file'));
            this.cartConnectorLink.set('href', decodeURIComponent(source.getAttribute('data-cart-connector-link')));
        }
        
        if (this.options.showSocialMediaButton) {
			this.socialmediabutton.set('data-link', decodeURIComponent(source.getAttribute('data-social-sharing-link')) );
		}

        $(document.body).fireEvent('updatecartlinks');

        // IE8 fix
        var longdesc = source.longdesc;
        if (longdesc == undefined) {
            longdesc = source.getAttribute('longDesc');
        }
        // END IE8 FIX


        // now lets set the image
        this.setImage(source.get('rel'), longdesc, source.getAttribute('data-description'), source.getAttribute('data-title'));
    },
    /**
     * Preloads one big image
     */
    loadNextImage: function () {
        if (!window.opera) {
            var thumbContainer = this.thumbs[this.loadedImages].getFirst();

            var imageToLoad = thumbContainer.get('rel');

            new Asset.image(imageToLoad, {
                onload: this.imageLoaded.bind(this, this.thumbs[this.loadedImages])
            });
        }
    },
    /**
     * Callback after an image has been successfully preloaded.
     * Removes the loading effects from the border div.
     * @param {HTMLElement} thumbContainer the thumb wrapper div
     */
    imageLoaded: function (thumbContainer) {
        this.loadedImages++;

        //remove loading styles
        thumbContainer.removeClass(this.options.loadingClass);
        if (this.loadedImages < this.thumbs.length) {
            this.loadNextImage();
        }
    },
    /**
     * Selects an image by its thumbnail index.
     * @param {integer} index of the thumbnail, starting with 0
     */
    selectByIndex: function (index) {
        //this.mouseLeaveHandler();
        if (index < 0 || this.thumbs.length <= index) {
            index = 0;
        }
        this.select(this.thumbs[index]);
    },
    /**
     *    Opposite to method above.
     *    @param {HTMLelement} container
     */
    deselect: function (container) {
        container.removeClass(this.options.activeClass);
    },
    /**
     *    Changes the full size image to given one.
     *    @param {String} newSrc, new target of the full size image
     *    @param {String} newText, new text for the info element
     */
    setImage: function (newSrc, newFullSizeImage, newText, newTitle) {

        if (this.effect === undefined) {
            this.effect = new Fx.Tween(this.bigImage, {duration: this.options.fadeSpeed, transition: Fx.Transitions.Quad.easeOut});
        } else {
            this.effect.cancel();
        }


        this.effect.start('opacity', 0).chain(function () {

            this.bigImage.set('src', newSrc);
            this.zoomLink.set('data-title', decodeURIComponent(newTitle));

            this.zoomLink.set('href', newFullSizeImage);

            EventGalleryMediabox.scanPage();
            try {
                if (eventGalleryMediaBoxImages && eventGalleryMediaBoxChangeImage) {
                    eventGalleryMediaBoxImages[0][0] = this.zoomLink.get('href');
                    eventGalleryMediaBoxImages[0][1] = this.zoomLink.getAttribute('data-title');
                    eventGalleryMediaBoxChangeImage(0);
                }
            }
            catch (e) {
            }

            if ($defined($(this.options.titleTarget))) {
                $(this.options.titleTarget).set('html', decodeURIComponent(newText));
            }
            //this.mouseLeaveHandler();
            this.effect.start('opacity', 1).chain(this.unBlockKeys.bind(this));

            // tack page event
            try {
                pageTracker._trackPageview();
            } catch (e) {
            }

        }.bind(this));

    },
    /**
     *    Navigates to previous page.
     */
    prevPage: function () {
        this.gotoPage(this.currentPageNumber - 1);
    },
    /**
     *    Navigates to next page.
     */
    nextPage: function () {
        this.gotoPage(this.currentPageNumber + 1);
    },
    /**
     *    Selects the previous image.
     */
    prevImage: function (e) {
        e.stop();
        this.selectByIndex(this.thumbs.indexOf(this.selectedContainer) - 1);


    },
    /**
     *    Selects the next image.
     */
    nextImage: function (e) {
        e.stop();
        this.selectByIndex(this.thumbs.indexOf(this.selectedContainer) + 1);
    },

    /**
     * Zooms an image
     */
    zoomImage: function (e) {
        e.stop();
    },
    /**
     *    Navigates to given page and selects the first image of it.
     *    Also hides the handles (if set).
     *    @param {Integer} pageNumber, index of the target page (0-n)
     *  @param {HTMLElement} selectImage, optionally receives a particular image to select
     */
    gotoPage: function (pageNumber, selectImage) {
        //if we like to select another image on that page than the first one
        if (pageNumber == 0) {
            selectImage = [selectImage, this.thumbs[0]].pick();
        } else {
            selectImage = [selectImage, this.thumbs[(pageNumber - 1) * this.imagesPerPage + this.imagesPerFirstPage]].pick();
        }

        if (pageNumber >= 0 && pageNumber < this.lastPage) {
            this.pageContainer.set('tween', {
                duration: this.options.pageSpeed,
                transition: Fx.Transitions.Quint.easeInOut
            });
            this.pageContainer.tween(
                'margin-left',
                this.pageContainer.getLast().getSize().x * pageNumber * -1
            );

            // fix height of the page-container
            var maxHeight = 0;
            $(this.pageContainer).getChildren().each(function (page) {
                var height = page.getSize().y;
                if (height > maxHeight) {
                    maxHeight = height;
                }
            });

            this.pageContainer.setStyle('height', maxHeight);

            this.currentPageNumber = pageNumber;
            this.select(selectImage);
            this.updateHandles();

        }
    },

    updateHandles: function () {
        //update handles
        if ($defined(this.options.prevHandle)) {
            new Fx.Tween(this.options.prevHandle, {link: 'link', duration: this.options.fadeSpeed * 2}).start('opacity', this.currentPageNumber == 0 ? 0 : 1);
        }
        if ($defined(this.options.nextHandle)) {
            new Fx.Tween(this.options.nextHandle, {link: 'cancel', duration: this.options.fadeSpeed * 2}).start('opacity', this.currentPageNumber == this.lastPage - 1 ? 0 : 1);
        }

        if ($defined(this.options.countHandle)) {
            this.updatePagingBar(this.options.countHandle, this.currentPageNumber, this.lastPage);
        }

    },

    updatePagingBar: function (countHandle, currentPage, pageCount) {

        //init the pagingbar
        if (countHandle.innerHTML == "") {

            for (var i = 0; i < pageCount; i++) {
                this.createCountLink(this, countHandle, i);
            }
        }

        var pageSpeed = this.options.pageSpeed;


        if (pageCount > 9) {

            for (var i = 0; i < pageCount; i++) {
                $('count' + i).setStyle('display', 'inline');
            }

            var skipFromRight = pageCount;
            var skipFromLeft = 0;

            var spaceToRight = pageCount - currentPage - 1;
            var spaceToLeft = currentPage;

            if (spaceToLeft > 4 && spaceToRight > 4) {
                skipFromLeft = currentPage - 4;
                skipFromRight = currentPage + 5;
            } else {
                if (spaceToLeft <= 4) {
                    skipFromLeft = 0;
                    skipFromRight = currentPage + 5 + (4 - spaceToLeft);
                }
                if (spaceToRight <= 4) {
                    skipFromLeft = currentPage - 4 - (4 - spaceToRight);
                    skipFromRight = pageCount;
                }
            }

            for (var i = 0; i < skipFromLeft; i++) {
                $('count' + i).setStyle('display', 'none');
            }

            for (var i = skipFromRight; i < pageCount; i++) {
                $('count' + i).setStyle('display', 'none');

            }


        }

        countHandle.getChildren('.active').removeClass('active');
        $('count' + currentPage).addClass('active');

    },


    createCountLink: function (gallery, countHandle, currentPageNumber) {

        var myAnchor = new Element('a', {
            href: '#',
            html: currentPageNumber + 1
        });


        myAnchor.addEvent('click', function (e) {
            this.gotoPage(currentPageNumber);
            return false;
        }.bind(gallery));

        var myListItem = new Element('li', {
            'class': 'count',
            id: 'count' + currentPageNumber
        });

        countHandle.grab(myListItem.grab(myAnchor));

    }


});