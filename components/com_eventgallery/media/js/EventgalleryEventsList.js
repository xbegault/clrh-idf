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
