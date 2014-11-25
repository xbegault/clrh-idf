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