    //   getFieldValue: function( option, raw = false ) {
    //     // var modelCID = this.getModelCID();
    //     // if ( modelCID ) {
    //     //   var settings = elementorFrontend.config.elements.data[ modelCID ];
    //     //   if ( settings ) {
    //     //     return settings.get( option );
    //     //   }
    //     // }

    //     // return this.getElementSettings( option );
    //   },

    //   isInner: function() {
    //     // return this.getFieldValue( 'isInner', true )
    //   },

    //   run: function() {

    //     // if ( this.isInner() ) {
    //     //   if ( elementorFrontend.getPageSettings( 'disable-anchors' ) !== 'yes' ) {
    //     //     var anchor = this.getFieldValue( 'slide-anchor' );
    //     //     if ( anchor ) {
    //     //       this.$element.attr( 'data-anchor', anchor );
    //     //     } else {
    //     //       this.$element.removeAttr( 'data-anchor' );
    //     //     }
    //     //   } else {
    //     //     this.$element.removeAttr( 'data-anchor' );
    //     //   }
    //     // } else {
    //     //   this.$element.addClass( 'my-section' );
    //     // }
    //   },

    //   onElementChange: function( option ) {
    //     // this.run();
    //   },

    //   onInit: function() {
    //     // elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
    //     // this.run();
    //   },

    //   onDestroy: function() {
    //   //   elementorModules.frontend.handlers.Base.prototype.onDestroy.apply(this, arguments);
    //   //   // TODO: anthing to deactivate?
    //   // }
    // } );

    // // elementorFrontend.hooks.addAction( 'frontend/element_ready/section', function( $element ) {
    // //   if ( 'section' === $element.data( 'element_type' ) ) {
    // //     new FrontEndExtended( {
    // //         $element: $element
    // //     } );
    // //   }
    // // });