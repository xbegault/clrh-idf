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
});