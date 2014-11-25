/* processes an image list*/
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
