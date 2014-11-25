
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

});