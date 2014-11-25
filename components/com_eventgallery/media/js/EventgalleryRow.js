/* processes a row is a image list */
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
});