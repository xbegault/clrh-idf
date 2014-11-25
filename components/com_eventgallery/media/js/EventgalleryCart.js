
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





