/* determines the size of an image so a image server can deliver it. */
var SizeCalculator = new Class({

    Implements: [Options],

    options: {
        // to be able to handle internal and google picasa images, we need to restrict the availabe image sizes.
        availableSizes: new Array(32, 48, 64, 72, 94, 104, 110, 128, 144, 150, 160, 200, 220, 288, 320, 400, 512, 576, 640, 720, 800, 912, 1024, 1152, 1280, 1440)
    },

    getSize: function (width, height, ratio) {

        var googleWidth = this.options.availableSizes[0];

        this.options.availableSizes.each(function (item, index) {
            if (googleWidth > 32) return;

            var lastItem = index == this.options.availableSizes.length - 1;

            if (ratio >= 1) {

                var widthOkay = item > width;
                var heightOkay = item / ratio > height


                if ((widthOkay && heightOkay) || lastItem) {

                    googleWidth = item;

                }
            } else {

                var heightOkay = item > height;
                var widthOkay = item * ratio > width;

                if ((widthOkay && heightOkay) || lastItem) {

                    googleWidth = item;

                }

            }


        }.bind(this));

        return googleWidth;
    },

    adjustImageURL: function (url, size) {
        url = url.replace(/width=(\d*)/, 'width=' + size);
        url = url.replace(/\/s(\d*)\//, '/s' + size + '/');
        url = url.replace(/\/s(\d*)-c\//, '/s' + size + '-c/');

        return url;
    }

});
var EventgalleryToolbox = new Class({
    /**
    * calculates the border of the given elements with the given properties
    */
    calcBorderWidth: function(elements, properties) {
        var sum = 0;

        for (var i=0; i<elements.length; i++) {
            for (var j=0; j<properties.length; j++) {
                var value = elements[i].getStyle(properties[j]).toInt();
                if (!isNaN(value)) {
                    sum += value;
                }
            }
        }
        
        return sum;    
    }
});
/*
 Class to manage an image. This can be the img tag or a container. It has to manage glue itself.
 */
var EventgalleryImage = new Class({
    Extends: EventgalleryToolbox,
    Implements: [Options],

    options: {
        maxImageHeight: 800
    },

    initialize: function (image, index, options) {
        this.setOptions(options);
        this.tag = image;
        this.index = index;
        this.calcSize();
    },
    calcSize: function () {
        // glue includes everything but the image width/heigt: margin, padding, border       
        var image = this.tag.getElement('img');
        
       if (image == null) {
			return;
       }
        var elements = [this.tag, image];

        this.glueLeft = this.calcBorderWidth(elements, ['padding-left', 'margin-left', 'border-width']);
        this.glueRight = this.calcBorderWidth(elements, ['padding-right', 'margin-right', 'border-width']);
        this.glueTop = this.calcBorderWidth(elements, ['padding-top', 'margin-top', 'border-width']);
        this.glueBottom = this.calcBorderWidth(elements, ['padding-bottom', 'margin-bottom', 'border-width']);    

        // get image size from data- attributes
       
        this.width = image.getProperty("data-width").toInt();//  - this.glueLeft - this.glueRight;
        this.height = image.getProperty("data-height").toInt();// - this.glueTop  - this.glueBottom;


        // fallback of data- attributes are not there
        if (this.width == null) {
            this.width = this.tag.getSize().x - this.glueLeft - this.glueRight;
        }

        if (this.height == null) {
            this.height = this.tag.getSize().y - this.glueTop - this.glueBottom;
        }
    },
    setSize: function (width, height) {

        // limit the maxium height of an image
        if (height > this.options.maxImageHeight) {
            width = Math.round(width / height * this.options.maxImageHeight);
            height = this.options.maxImageHeight;
        }


        var newWidth = width - this.glueLeft - this.glueRight;
        var newHeight = height - this.glueTop - this.glueBottom;


        if (this.width < newWidth) {
            newWidth = this.width;
        }


        if (this.height < newHeight) {
            newHeight = this.height;

        }


        var ratio = this.width / this.height;

        //console.log("the size of the image should be: "+width+"x"+height+" so I have to set it to: "+newWidth+"x"+newHeight);


        var sizeCalculator = new SizeCalculator();
        var googleWidth = sizeCalculator.getSize(newWidth, newHeight, ratio);


        //adjust background images
        var image = this.tag.getElement('img');
		if (image == null) {
			return;
		}
        // set a new background image
        var backgroundImageStyle = image.getStyle('background-image');
        var longDesc = image.getAttribute('longDesc');
        if (!longDesc) {
            longDesc = "";
        }

        backgroundImageStyle = sizeCalculator.adjustImageURL(backgroundImageStyle, googleWidth);
        longDesc = sizeCalculator.adjustImageURL(longDesc, googleWidth);


        image.setStyle('background-image', backgroundImageStyle);
        image.set('longDesc', longDesc);
        image.setStyle('background-position', '50% 50%');
        image.setStyle('background-repeat', 'no-repeat');
        image.setStyle('display', 'block');
        image.setStyle('margin', 'auto');

		// IE8 fix: check the width/height first
		if (newWidth>0) {
        	image.setStyle('width', newWidth);
        }
        if (newHeight>0) {
        	image.setStyle('height', newHeight);
        }

    }

});/* processes a row is a image list */
var EventgalleryRow = new Class({
    Implements: [Options],

    options: {
        maxWidth: 960,
        maxHeight: 150,
        heightJitter: 0,
        adjustHeight: true,
        cropLastImage: true
    },


    initialize: function (options) {
        this.setOptions(options);
        this.images = new Array();
        this.width = 0;
        if (this.options.heightJitter > 0) {
            this.options.maxHeight = Math.floor(this.options.maxHeight + (Math.random() * 2 * this.options.heightJitter) - this.options.heightJitter);
        }

    },
    add: function (eventgalleryImage) {
        var imageWidth = Math.floor(eventgalleryImage.width / eventgalleryImage.height * this.options.maxHeight);

        // determine the number of images per line. return false if the row if full.
        if (this.width + imageWidth <= this.options.maxWidth || this.images.length == 0) {
            this.images.push(eventgalleryImage);
            this.width = this.width + imageWidth;
            return true;
        } else {
            return false;
        }

    },
    /**
    * @param bool finalRows defines is this row is part of a set of final rows. 
    */
    processRow: function (finalRows) {

        // calc the diff
        var diff = this.options.maxWidth - this.width;
        var diffWidth = Math.floor(diff / this.images.length);

        if (diffWidth > this.options.rowWidth / this.images.length) {
            diffWidth = 0;
        }

        //console.log("process row. DiffWidth="+diffWidth);

        // determine a common height for the images
        var diffHeight = Math.floor(diff / this.images.length);
        if (this.options.adjustHeight == false) {
            diffHeight = 0;
        }

        // do not shrink a line
        if (diffHeight < 0) {
            diffHeight = 0;
        }

        var diffWidthBalance = diff - (diffWidth * (this.images.length - 1));

        // handle the last image differently if it should not be cropped. Be aware that a vertial image will appear in full height!
        
        if (
                // display the last/first image with the available width if:
                //
                // the image should not be cropped if we display the first image
                (this.images.length == 1 && !this.options.cropLastImage)|| 
                // this is for the last image but only if it's landscaped. This avoids square
                // sized images for landscape images. We just override the crop configuration
                ( finalRows && this.images.length == 1 && this.images[0].height<this.images[0].width)
            ) 
        {
            var image = this.images[0];
            var height = Math.round(image.height / image.width * this.options.maxWidth);
            image.setSize(this.options.maxWidth, height);

        
        } else {
            this.images.each(function (image, index) {

                var imageWidth = Math.floor(image.width / image.height * this.options.maxHeight);

                if (index == this.images.length - 1) {
                    image.setSize(imageWidth + diffWidthBalance, this.options.maxHeight + diffHeight);
                } else {
                    image.setSize(imageWidth + diffWidth, this.options.maxHeight + diffHeight);
                }

            }.bind(this));
        }

    }
});/* processes an image list*/
var EventgalleryImagelist = new Class({
    Implements: [Options],
    options: {
        rowHeightPercentage: 100,
        rowHeight: 150,
        rowHeightJitter: 0,
        minImageWidth: 150,
        cropLastImage: true,
        // the object where we try to get the width from 
        imagesetContainer: null,
		// the object containing all the images elements. Usually they are retieved with a selector like '.imagelist a',
        imageset: null,       
        firstImageRowHeight: 2,
        initComplete: function () {
        },
        resizeStart: function () {
        },
        resizeComplete: function () {
        }
    },
	
	images: Array(),
	// used to compare for width changes
	eventgalleryPageWidth: 0,
	// the width of the container. This is kind of tricky since there can be many containers or just one.
	width: null,
		
	
    initialize: function (options) {
        this.setOptions(options);
        
        this.width = this.options.imagesetContainer.getSize().x;
        
        var images_tags = this.options.imageset;
        this.images = Array();

        images_tags.each(function (item, index, obj) {
            this.images.push(new EventgalleryImage(item, index));
        }.bind(this));

        window.addEvent('resize', function () {
            window.clearTimeout(this.eventgalleryTimer);

            this.eventgalleryTimer = (function () {
                var new_width = this.options.imagesetContainer.getSize().x;
                this.width = new_width;
                if (this.eventgalleryPageWidth != new_width) {
                    this.options.resizeStart();
                    this.eventgalleryPageWidth = new_width;
                    this.processList();
                    this.options.resizeComplete();
                }
            }.bind(this)).delay(500);

        }.bind(this));

		this.options.imagesetContainer.setStyle('min-height', this.options.rowHeight*this.images.length);
			
        this.processList();

		this.options.imagesetContainer.setStyle('min-height','0px');

        this.options.initComplete();
    },
    /*calculated the with of an element*/
    getRowWidth: function () {
        var rowWidth = this.width;

        /* fix for the internet explorer if width if 45.666% == 699.87px*/
        if (window.getComputedStyle) {
            rowWidth = Math.floor(window.getComputedStyle(this.options.imagesetContainer).width.toFloat()) - 1;
        } else {
            rowWidth = rowWidth - 1;
        }

        return rowWidth;
    },

    /* processes the image list*/
    processList: function () {

        /* find out how much space we have*/
        var rowWidth = this.getRowWidth();


        /* get a copy of the image list because we will pop the image during iteration*/
        var imagesToProcess = Array.clone(this.images);

        /* display the first image larger */
        if (this.options.firstImageRowHeight > 1) {
            var image = imagesToProcess.shift();

            /*if we have a large image, we have to hide it to get the real available space*/
            image.tag.setStyle('display', 'none');
            rowWidth = this.getRowWidth();
            image.tag.setStyle('display', 'block');

            var imageHeight = this.options.firstImageRowHeight * this.options.rowHeight;
            var imageWidth = Math.floor(image.width / image.height * imageHeight);

            if (imageWidth + this.options.minImageWidth >= rowWidth) {
                imageWidth = rowWidth;
                rowsLeft = 0;
            }

            image.setSize(imageWidth, imageHeight);

            var options = {
                maxWidth: rowWidth - imageWidth,
                maxHeight: this.options.rowHeight,
                adjustHeight: false
            };

            if (options.maxWidth > 0) {
                this.generateRows(imagesToProcess, this.options.firstImageRowHeight, options, false);
            }
        }

        var options = {
            maxWidth: rowWidth,
            maxHeight: this.options.rowHeight,
            heightJitter: this.options.rowHeightJitter,
            cropLastImage: this.options.cropLastImage
        };

        this.generateRows(imagesToProcess, 99999, options, true);

    },

    /**
    * @param bool finalRows defines if this set is the last set of rows.
    */
    generateRows: function (imagesToProcess, numberOfRowsToCreate, options, finalRows) {


        var currentRow = new EventgalleryRow(options);

        while (imagesToProcess.length > 0 && numberOfRowsToCreate > 0) {
            var addSuccessfull = currentRow.add(imagesToProcess[0]);
            if (addSuccessfull) {
                imagesToProcess.shift();
            } else {
                currentRow.processRow(finalRows);
                numberOfRowsToCreate--;
                if (numberOfRowsToCreate == 0) break;
                currentRow = new EventgalleryRow(options);
            }
        }

        currentRow.processRow(finalRows);

    }

});
/* processes a list of images and tries to resize  separately*/
var EventgalleryEventsList = new Class({
    Extends: EventgalleryImagelist,
    Implements: [Options],

    /* processes the image list*/
    processList: function () {
        var width = this.width;
        this.images.each(function (item) {
            var height = Math.ceil(width * this.options.rowHeightPercentage / 100);
            item.setSize(width, height);
        }.bind(this));

    }
});
/* processes a list of images and tries to resize separately*/
var EventgalleryEventsTiles = new Class({
    Extends: EventgalleryImagelist,
    eventgalleryTilesCollection: null,		  
   
    processList: function () {
    	
        var width = this.width;
      	 
        this.images.each(function (image) {
            var newHeight = Math.round(image.height / image.width * width);
            var newWidth = width;
            if (this.options.adjustMode == "height" && image.height>image.width) {
                newHeight = width;
                newWidth = Math.round(image.width / image.height * newHeight);
            }

            image.setSize(newWidth, newHeight);
            
        }.bind(this));
        
    }
});// create a grid layout and centers images in a tile
var EventgalleryGridCollection = new Class({
    Extends: EventgalleryToolbox,
    Implements: [Options],

    options: {
        tilesSelector: '#events-tiles .event',
        tilesContainerSelector: '#events-tiles .event-tiles',
        thumbSelector: '.event-thumbnail',
        thumbContainerSelector: '.event-thumbnails'
    },        
    tiles: null,
    tilesContainer: null,
    
    initialize: function (options) {
        this.setOptions(options);
        this.tiles = $$(this.options.tilesSelector);
        this.tilesContainer = $$(this.options.tilesContainerSelector);
        //this.calculate();
    },

    calculate: function() {

        var tilesPerRow = 1;
         // reset grid to support resize and media queries
        this.tiles.setStyles({
            visibility: 'hidden',
            position: 'static',
            'float': 'left'
        });

        // calculate tiles per row    
        var y = this.tiles[0].getPosition().y;

        for(var i=1; i<this.tiles.length; i++) {
            if (this.tiles[i].getPosition().y != y) {
                break;
            }
            tilesPerRow++;
        }


        var columnWidth = this.tiles[0].getSize().x;
        var currentColumn = 0;
        var currentRow = 0;
        
        // doing this loop multiple times increases the performance due to 
        // the fact that we can avoid size recalculations.
        this.tiles.each(function(tile) {
            tile.setStyle('left', currentColumn*columnWidth);
            tile.setStyle('top', currentRow*columnWidth);                        
            tile.setStyle('height', columnWidth);
            
            currentColumn++;
            if (currentColumn+1>tilesPerRow) {
                currentColumn = 0;                
                currentRow++;
            }
            
            
        }.bind(this));
        
        // calculate center images date
        this.tiles.each(function(tile) {  
            var thumb = tile.getElements(this.options.thumbSelector)[0];
            var thumbContainer = tile.getElements(this.options.thumbContainerSelector)[0];
            var tileSize = tile.getSize()
            
            var thumbSize = thumb.getSize();
            
            var tileWidth = tileSize.x - this.calcBorderWidth([tile], ['padding-right', 'margin-right', 'border-width', 'padding-left', 'margin-left', 'border-width']);
            
            var adjustX = Math.floor((tileWidth - thumbSize.x)/2);
            var adjustY = Math.floor((tileSize.y - thumbSize.y)/2);
            
            thumbContainer.setAttribute('data-adjust-x', adjustX);
            thumbContainer.setAttribute('data-adjust-y', adjustY);            
        }.bind(this));
        
        // center images
        this.tiles.each(function(tile) {  
        	var thumbContainer = tile.getElements(this.options.thumbContainerSelector)[0];
            thumbContainer.setStyle('left', thumbContainer.getAttribute('data-adjust-x')+'px' );
            thumbContainer.setStyle('top',  thumbContainer.getAttribute('data-adjust-y')+'px' );
        }.bind(this));        

        var overallHeight = Math.ceil(this.tiles.length/tilesPerRow)*columnWidth;
        this.tilesContainer.setStyle('height', overallHeight);

        this.tiles.setStyles({
            visibility: 'visible',
            position: 'absolute',
            'float': 'none'
        });

    }

});var EventgalleryTilesCollection = new Class({

    Implements: [Options],

    options: {
        tilesSelector: '#events-tiles .event',
        tilesContainerSelector: '#events-tiles .event-tiles'
    },        
    tiles: null,
    tilesContainer: null,
	
    initialize: function (options) {
        this.setOptions(options);
        this.tiles = $$(this.options.tilesSelector);
        this.tilesContainer = $$(this.options.tilesContainerSelector);
        //this.calculate();
    },
    calculate: function() {
    	var tilesPerRow = 1;
    	 // reset grid to support resize and media queries
        this.tiles.each(function(tile) {
            tile.setStyle('visibility', 'hidden');
            tile.setStyle('position', 'static');
            tile.setStyle('float', 'left');
        });

        // calculate tiles per row    
        var y = this.tiles[0].getPosition().y;

        for(var i=1; i<this.tiles.length; i++) {
            if (this.tiles[i].getPosition().y != y) {
                break;
            }
            tilesPerRow++;
        }
        

        // create array of height values for the columns
        var columnHeight = [];
        for (var i=0; i<tilesPerRow; i++) {
            columnHeight.push(0);
        }

        var columnWidth = this.tiles[0].getSize().x;

        this.tiles.each(function(tile) {

            var smallestColumn = this.getSmallestColumn(columnHeight);

            tile.setStyle('left', smallestColumn*columnWidth);
            tile.setStyle('top', columnHeight[smallestColumn]);

            columnHeight[smallestColumn] = columnHeight[smallestColumn]+tile.getSize().y;            

        }.bind(this));
        
        this.tiles.each(function(tile) {
            tile.setStyle('visibility', 'visible');
            tile.setStyle('position', 'absolute');
            tile.setStyle('float', 'none');
        });

        this.tilesContainer.setStyle('height', columnHeight[this.getHighestColumn(columnHeight)]);
    },
    /* 
    * returns the position of the smallest value in the array
    */	
	getSmallestColumn: function(columnHeight) {

        var smallestColumnValue = columnHeight[0];
        var smallestColumnNumber = 0;
        
        for(var i=0; i<columnHeight.length; i++) {
            if (smallestColumnValue>columnHeight[i]) {
                smallestColumnValue=columnHeight[i];
                smallestColumnNumber = i;
            }
        
        }
        return smallestColumnNumber;

    },
	/* 
    * returns the position of the highest value in the array
    */
    getHighestColumn: function(columnHeight) {

        var columnValue = columnHeight[0];
        var columnNumber = 0;
        
        for(var i=0; i<columnHeight.length; i++) {
            if (columnValue<columnHeight[i]) {
                columnValue=columnHeight[i];
                columnNumber = i;
            }
        
        }
        return columnNumber;

    }
    
});
/*
 This is our cart class.
 */
var EventgalleryCart = new Class({
    Implements: [Options],
    cart: new Array(),
    options: {
        buttonShowType: 'block',
        emptyCartSelector: '.eventgallery-cart-empty',
        cartSelector: '.eventgallery-ajaxcart',
        cartItemContainerSelector: '.cart-items-container',
        cartItemsSelector: '.eventgallery-ajaxcart .cart-items',
        cartItemSelector: '.eventgallery-ajaxcart .cart-items .cart-item',
        cartCountSelector: '.itemscount',
        buttonDownSelector: '.toggle-down',
        buttonUpSelector: '.toggle-up',
        cartItemsMinHeight: null,
        removeUrl: "",
        add2cartUrl: "",
        getCartUrl: "",
        removeLinkTitle: "Remove"

    },

    initialize: function (options) {
        this.setOptions(options);

        this.myVerticalSlide = new Fx.Tween($$(this.options.cartItemContainerSelector).getLast(), {
            duration: 'short',
            transition: 'quad:in',
            link: 'cancel',
            property: 'height'
        });

        $$(this.options.buttonDownSelector).addEvent('click', function (event) {
            event.stop();
            this.myVerticalSlide.start($$(this.options.cartItemsSelector).getLast().getSize().y);
            $$(this.options.buttonDownSelector).setStyle('display', 'none');
            $$(this.options.buttonUpSelector).setStyle('display', this.options.buttonShowType);
        }.bind(this));

        $$(this.options.buttonUpSelector).addEvent('click', function (event) {
            event.stop();
            this.myVerticalSlide.start(this.options.cartItemsMinHeight);
            $$(this.options.buttonUpSelector).setStyle('display', 'none');
            $$(this.options.buttonDownSelector).setStyle('display', this.options.buttonShowType);
        }.bind(this));


        $(document.body).removeEvents('click:relay(.eventgallery-add2cart)');
        $(document.body).addEvent('click:relay(.eventgallery-add2cart)', function (e) {
            this.add2cart(e);
        }.bind(this));

        $(document.body).removeEvents('click:relay(.eventgallery-add-all)');
        $(document.body).addEvent('click:relay(.eventgallery-add-all)', function (e) {
            this.addAll2cart(e)
        }.bind(this));

        $(document.body).removeEvents('click:relay(.eventgallery-removeFromCart)');
        $(document.body).addEvent('click:relay(.eventgallery-removeFromCart)', function (e) {
            this.removeFromCart(e)
        }.bind(this));

        $(document.body).addEvent('updatecartlinks', function (e) {
            this.populateCart(true);
        }.bind(this));

        $(document.body).addEvent('updatecart', function (e) {
            this.cart = e.cart;
            this.populateCart(false);
        }.bind(this));

        this.updateCart();

    },

    updateCartItemContainer: function () {

        // detect multiple rows

        var multiline = false;
        var y = -1;

        $$(this.options.cartItemSelector).each(function (item) {
            var posY = item.getPosition().y;
            if (y < 0) {
                y = posY;
            } else if (y != posY) {
                multiline = true;
            }
        }.bind(this));

        if (multiline) {
            // prevent showing the wrong button. Basically this is an inital action if a second row is created.

            var down = $$(this.options.buttonDownSelector);
            var up = $$(this.options.buttonUpSelector);
            if (down.getStyle('display') == 'none' && up.getStyle('display') == 'none') {
                down.setStyle('display', this.options.buttonShowType);
            } else {
                // update if a third or more row is created
                if (up.getStyle('display') != 'none') {
                    // timeout to avoid any size issues because of a slow browser
                    setTimeout(function() {
                        this.myVerticalSlide.stop();
                        this.myVerticalSlide.start($$(this.options.cartItemsSelector).getLast().getSize().y);  
                    }.bind(this), 1000);                    
                }
            }
        } else {
            this.myVerticalSlide.start(this.options.cartItemsMinHeight);
            $$(this.options.buttonUpSelector).setStyle('display', 'none');
            $$(this.options.buttonDownSelector).setStyle('display', 'none');
        }
    },

    /* Populate the cart element on the page with the data we used */
    populateCart: function (linksOnly) {

        
        var fx1 = new Fx.Slide($$(this.options.cartSelector)[0], {resetHeight : true});
        if (this.cart.length == 0) {
            
            fx1.slideOut();
            $$(this.options.emptyCartSelector).setStyle('display', 'block');            
            //$$(this.options.cartSelector).setStyle('display', 'none');
        } else {
            $$(this.options.cartSelector).setStyle('display', 'block');            
            fx1.slideIn();

            
            $$(this.options.emptyCartSelector).setStyle('display', 'none');
        }
        // define where all the cart html items are located

        var cartHTML = $$(this.options.cartItemsSelector).getLast();
        if (cartHTML == null) {
            return;
        }
        // clear the html showing the current cart
        if (!linksOnly) {
            cartHTML.set('html', "");
        }

        // reset cart button icons
        $$('a.eventgallery-add2cart').addClass('button-add2cart').removeClass('button-alreadyInCart');


        for (var i = this.cart.length - 1; i >= 0; i--) {
            //create the id. It's always folder=foo&file=bar
            var id = 'folder=' + this.cart[i].folder + '&file=' + this.cart[i].file;
            //add the item to the cart. Currently we simple refresh the whole cart.
            if (!linksOnly) {
                cartHTML.set('html', cartHTML.get('html') + 
                    '<div class="cart-item"><span class="badge">'+this.cart[i].count+'</span>' + 
                        this.cart[i].imagetag + 
                        '<a href="#" title="' + this.options.removeLinkTitle + '" class="eventgallery-removeFromCart button-removeFromCart" data-id="lineitemid=' + this.cart[i].lineitemid + '">'+
                        '<i class="big"></i>' +
                        '</a></div>');
            }
            // mark the add2cart link to show the item is already in the cart
            $$('a.eventgallery-add2cart[data-id="' + id + '"]').addClass('button-alreadyInCart').removeClass('button-add2cart');

        }
        ;

        if (!linksOnly) {
            cartHTML.set('html', cartHTML.get('html') + '<div style="clear:both"></div>');
            if (null == this.options.cartItemsMinHeight) {
                this.options.cartItemsMinHeight = $$(this.options.cartItemContainerSelector).getLast().getSize().y;
            }
            this.updateCartItemContainer();
        }

        $$('.itemscount').set('html', this.cart.length);
        EventGalleryMediabox.scanPage();
    },

    /* Get the current version of the cart from the server */
    updateCart: function () {
        var jsonReq = new Request.JSON({
            url: this.options.getCartUrl,
            method: 'post',
            data: {
                json: 'yes'
            },
            onComplete: function (response) {
                if (response !== undefined) {
                    $(document.body).fireEvent('updatecart',
                        {
                            target: null,
                            cart: response,
                            stop: function(){},
                            preventDefault: function(){},
                            stopPropagation: function(){}
                        }
                    );
                }
            }.bind(this)
        }).send();

    },

    /* Send a request to the server to remove an item from the cart */
    removeFromCart: function (event) {
        return this.doRequest(event, this.options.removeUrl);
    },

    /* Send a request to the server to add an item to the cart */
    add2cart: function (event) {
        
        var radioButtons = $$('input:checked[name=currentimagetype]');
        if (radioButtons.length>0) {
            if (event.target.tagName == 'A') {
                linkElement = $(event.target);
            } else {
                linkElement = $(event.target).getParent('A');
            }
            
            var data = linkElement.getAttribute('data-id');
            data = data + '&imagetypeid=' + radioButtons[0].value;

            return this.doRequest(event, this.options.add2cartUrl, data);
        } else {
            return this.doRequest(event, this.options.add2cartUrl);
        }
    },

     /* Send a request to the server to add an item to the cart */
    addAll2cart: function (event) {
        
        var radioButtons = $$('input:checked[name=currentimagetype]');
        var items = [];

        $$('.eventgallery-add2cart').each(function(item) {
            //var data = item.getAttribute('data-id');
            //data = data + '&imagetypeid=' + radioButtons[0].value;
            //items.push(data);
            $(document.body).fireEvent('click:relay(.eventgallery-add2cart)',
            {
                target: item,
                stop: function(){},
                preventDefault: function(){},
                stopPropagation: function(){}
            });

        });

    },

    /* do the request and care about the clicked buttons. */
    doRequest: function (event, url, data) {
        var linkElement;

        if (event.target.tagName == 'A') {
            linkElement = $(event.target);
        } else {
            linkElement = $(event.target).getParent('A');
        }

        var iconElement = linkElement.getChildren('i').getLast();
        if (data == undefined) {
            var data = linkElement.getAttribute('data-id');
        }
        var imagetype = $$('input:checked[name=currentimagetype]')[0];
        
        iconElement.addClass('loading');

        var oldBtnClass = "";
        if (linkElement.hasClass('btn-primary')) {
            oldBtnClass = 'btn-primary';
            linkElement.removeClass(oldBtnClass);
            linkElement.addClass('btn-info');
        }
        var myRequest = new Request.JSON(
            {
                url: url,
                method: "POST",
                data: data,
                onComplete: function (response) {
                    if (response !== undefined) {
                        $(document.body).fireEvent('updatecart',
                            {
                                target: null,
                                cart: response,
                                stop: function(){},
                                preventDefault: function(){},
                                stopPropagation: function(){}
                            }
                        );
                    }
                    iconElement.removeClass('loading');

                    if (oldBtnClass.length>0) {
                        linkElement.removeClass('btn-info');
                        linkElement.addClass('btn-success');
                        setTimeout(function() {
                            linkElement.removeClass('btn-success');
                            linkElement.addClass(oldBtnClass);
                        }.bind(this), 2000); 
                    }

                }.bind(this)
            }
        ).send();
        event.stopPropagation();
        event.preventDefault();
        return true;
    }
});





window.addEvent("domready", function() {


	$(document.body).addEvent('click:relay(a.social-share-button-close)', function(e) {
		e.preventDefault();
		var myDiv = $(e.target).getParent('.social-sharing-toolbox');
		var myFx = new Fx.Tween(myDiv, {
		    property: 'opacity',
		    onComplete : function() {myDiv.dispose();}
		});
		myFx.start(0);		
	});	
		
	
			
	$(document.body).addEvent('click:relay(a.social-share-button-open)', function(e) {

		e.preventDefault();

		var link = $(e.target);
		if (!link.getAttribute('data-link')) {
			link = link.getParent('a');	
		}
		var id = 'id_' + Math.ceil(Math.random()*10000000);
		
		var targetPos = $(e.target).getPosition();
		var myDiv = new Element('div', {
		    href: '#',
		    'class': 'social-sharing-toolbox',
		    html: 'Loading',
		    id: id,
		    styles: {
		    	'opacity': '1 !important',
		    	'position': 'absolute',    	
		    	'top': targetPos.y-10,
		    	'left': targetPos.x-10,
		    	'opacity': 0
		    }
		});		
	
		
		$$('body')[0].grab(myDiv);
		
		myDiv.fade('in');
		myDiv.set('load', {evalScripts: true});			
		myDiv.load(link.getAttribute('data-link'));

		var timer = null;
					
		var closeFunction = function(){			
			var myFx = new Fx.Tween(myDiv, {
			    property: 'opacity',
			    onComplete : function() {myDiv.dispose();}
			});
			myFx.start(0);
			$$('body').removeEvent('click', closeFunction2);
		}		
		
		myDiv.addEvent('mouseleave', function(){
			timer = window.setTimeout(closeFunction, 1000);
		}.bind(this));
		
		myDiv.addEvent('mouseenter', function() {
			window.clearTimeout(timer);			
		}.bind(this));
		
		
		// this method is used to close the sharing windows if we click somewhere else.
		var closeFunction2 = function(e) {
			//console.log($(e.target).getParent('#' + id));
			if (e.target.id != id && $(e.target).getParent('#' + id) == null) {
				closeFunction();
			}
			//console.log('close2');
		}.bind(this);
		
		$$('body').addEvent('click', closeFunction2);			
		
	}); 
});