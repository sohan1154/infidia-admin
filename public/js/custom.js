$('#category_name').on('change', function() {
  var cat_id = this.value;	
  $("#subcategory_name").html('');
  $("#extraAttribute").html('');
  $cathtml = '';
  if(cat_id!=''){
	  $.ajax({
	    type: 'get',
	    url: 'subcategories/'+cat_id,
	    success: function(results) {
	    	$cathtml = '<option value="">Select Sub Category</option>';
	    	var data = JSON.parse(results);
	    	$.each(data, function(i, item) {
			    $cathtml += '<option value="'+item.id+'">'+item.name+'</option>';			    			    
			});
			$('#subcategory_name').append($cathtml);
			if(cat_id!=''){
				  $.ajax({
				    type: 'get',
				    url: 'getAttribute/'+cat_id,
				    success: function(result) {
				    	$("#attribute_set_id").val(result);
				    	if(result!='' || result!='0'){
				    		$.ajax({
							    type: 'get',
							    dataType: 'html',
							    url: 'setproductProperties/'+result,
							    success: function(extraAttr) {
							    	$("#extraAttribute").append(extraAttr);	
							    }
							});
				    	}
				    }
				  });
			}
	    }
	  });
  } else {
  	 $cathtml = '<option value="">Select Sub Category</option>';
  	 $('#subcategory_name').append($cathtml);
  }
   //
});

$('.RemoveImage').click(function() {
  var imageId = $(this).attr('id').replace('removeId','');
  var baseUrl = $('#baseUrl').val();
  var pid     = $(this).attr('data-pid');
  if(imageId!=''){
    $.ajax({
      type: 'post',
      data:{'id':imageId,'pid':pid},
      url: baseUrl+'/removeImage',
      success: function(data) {
        $(".imageTag"+imageId).remove();return false;
      }
    });
  };
});

$('.RemoveAttrImage').click(function() {
  var imageId = $(this).attr('id').replace('removeAttrId','');
  var baseUrl = $('#baseUrl').val();
  var attrId     = $(this).attr('data-attrId');
  if(imageId!=''){
    $.ajax({
      type: 'post',
      data:{'id':imageId,'attrId':attrId},
      url: baseUrl+'/removeAttrImage',
      success: function(data) {
        $(".attrImageTag-"+attrId+'-'+imageId).remove();
        return false;
      }
    });
  };
});

$('.attr_remove-button-edit').click(function() {
  var baseUrl = $('#baseUrl').val();
  var pid     = $(this).attr('data-id');
  if(pid!=''){
    $.ajax({
      type: 'post',
      data:{'id':pid},
      url: baseUrl+'/removeAttrData',
      success: function(data) {
        $("#compData"+pid).remove();return false;
      }
    });
  };
});  

/**
  * Basic jQuery Validation Form Demo Code
  * Copyright Sam Deering 2012
  * Licence: http://www.jquery4u.com/license/
  */
(function($,W,D)
{
    var JQUERY4U = {};

    JQUERY4U.UTIL =
    {
        setupFormValidation: function()
        { 
            //form validation rules
            $("#AddProduct1").validate({
                rules: {
                    name: "required",
                    user_id: "required",
                    base_price: "required",
                    /*email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 5
                    },
                    agree: "required"*/
                },
                messages: {
                    user_id: "Please select seller name",
                    name: "Please enter product name",
                    /*password: {
                        required: "Please provide a password",
                        minlength: "Your password must be at least 5 characters long"
                    },*/
                    base_price: "Please enter a base price"
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
        }
    }

    //when the dom has loaded setup form validation rules
    $(D).ready(function($) {
        JQUERY4U.UTIL.setupFormValidation();
    });

})(jQuery, window, document);

$(document).ready(function () {
  $.validator.addMethod("atLeastOneLetter", function (value, element) {
    return this.optional(element) || /[a-zA-Z]+/.test(value);
  }, "Please enter a valid banner name");
  $.validator.addMethod("atLeastOneLettercatName", function (value, element) {
    return this.optional(element) || /[a-zA-Z]+/.test(value);
  }, "Please enter a valid category name");
   $.validator.addMethod("atLeastOneLetterplan", function (value, element) {
    return this.optional(element) || /[a-zA-Z]+/.test(value);
  }, "Please enter a valid paln name");
  $.validator.addMethod("atLeastOneLetteruser", function (value, element) {
    return this.optional(element) || /[a-zA-Z]+/.test(value);
  }, "Please enter a valid user name");
  $.validator.addMethod("atLeastOneLetterpage", function (value, element) {
    return this.optional(element) || /[a-zA-Z]+/.test(value);
  }, "Please enter a valid page name");
  $.validator.addMethod("atLeastOneLetterattr", function (value, element) {
    return this.optional(element) || /[a-zA-Z]+/.test(value);
  }, "Please enter a valid attribute name");
  jQuery.validator.addMethod('ckrequired', function (value, element, params) {
    var idname = jQuery(element).attr('id');
    var messageLength =  jQuery.trim ( CKEDITOR.instances[idname].getData() );
    return !params  || messageLength.length !== 0;
  }, "Please enter description");
  jQuery.validator.addMethod('ckrequiredpage', function (value, element, params) {
    var idname = jQuery(element).attr('id');
    var messageLength =  jQuery.trim ( CKEDITOR.instances[idname].getData() );
    return !params  || messageLength.length !== 0;
  }, "Please enter a page description");
  jQuery.validator.addMethod('ckrequiredplan', function (value, element, params) {
    var idname = jQuery(element).attr('id');
    var messageLength =  jQuery.trim ( CKEDITOR.instances[idname].getData() );
    return !params  || messageLength.length !== 0;
  }, "Please enter a plan feature");
  jQuery.validator.addMethod("noSpace", function(value, element) { 
    var spaceCount = value.split(" ").length - 1;
     return spaceCount==0; 
    }, "Please enter valid SKU. No space!");
  jQuery.validator.addMethod("alreadySKU", function(value, element) { 
    alert(value);
    var spaceCount = value.split(" ").length - 1;
     return spaceCount==0; 
    }, "Please enter valid SKU.No space!");
  $( "#bannerForm" ).validate( {
        ignore: [],
        rules: {
          banner_name: {
            required: true,
            minlength: 3,
            maxlength: 150,
            atLeastOneLetter: true
          },          
          banner_description: {
            ckrequired:true,
          },
          role: {
            required: true,
          },
          banner_image: {
            required: true,
            accept:"jpg,png,jpeg,gif"
          },
          status: {
            required: true,
          },
        },
        messages: {
          banner_name: {
            required: "Please enter a banner name",
            minlength: "Please enter at least {0} characters",
            maxlength: "Please enter no more than {0} characters",
          },          
          banner_description: {
            required: "Please enter banner description",
          },
          role: {
            required: "Please select user role",
          },
          banner_image: {
            required: "Please select banner image",
            accept: "Only image type jpg/png/jpeg/gif is allowed"
          },
          status: {
            required: "Please select status",
          },
        },
        errorElement: "em",
        errorPlacement: function ( error, element ) {
          // Add the `help-block` class to the error element
          error.addClass( "help-block " );

          // Add `has-feedback` class to the parent div.form-group
          // in order to add icons to inputs
          element.parents( ".col-sm-5" ).addClass( "has-feedback" );

          if ( element.prop( "type" ) === "checkbox" ) {
            error.insertAfter( element.parent( "label" ) );
          } else if ( element.prop( "type" ) === "textarea" ) {
            error.insertAfter( element.parent( "div" ) );
          } else {
            error.insertAfter( element );
          }

          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !element.next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-remove form-control-feedback'></span>" ).insertAfter( element );
          }
        },
        success: function ( label, element ) {
          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !$( element ).next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-ok form-control-feedback'></span>" ).insertAfter( $( element ) );
          }
        },
        highlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
          $( element ).next( "span" ).addClass( "glyphicon-remove" ).removeClass( "glyphicon-ok" );
        },
        unhighlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
          $( element ).next( "span" ).addClass( "glyphicon-ok" ).removeClass( "glyphicon-remove" );
        }
  });
  $( "#attrForm" ).validate( {
        ignore: [],
        rules: {
          name: {
            required: true,
            minlength: 3,
            maxlength: 150,
            atLeastOneLetterattr: true
          },
          type: {
            required: true,
          },
          status: {
            required: true,
          },
        },
        messages: {
          name: {
            required: "Please enter a attribute name",
            minlength: "Please enter at least {0} characters",
            maxlength: "Please enter no more than {0} characters",
          }, 
          type: {
            required: "Please select type",
          },
          status: {
            required: "Please select status",
          },
        },
        errorElement: "em",
        errorPlacement: function ( error, element ) {
          // Add the `help-block` class to the error element
          error.addClass( "help-block " );

          // Add `has-feedback` class to the parent div.form-group
          // in order to add icons to inputs
          element.parents( ".col-sm-5" ).addClass( "has-feedback" );

          if ( element.prop( "type" ) === "checkbox" ) {
            error.insertAfter( element.parent( "label" ) );
          } else if ( element.prop( "type" ) === "textarea" ) {
            error.insertAfter( element.parent( "div" ) );
          } else {
            error.insertAfter( element );
          }

          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !element.next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-remove form-control-feedback'></span>" ).insertAfter( element );
          }
        },
        success: function ( label, element ) {
          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !$( element ).next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-ok form-control-feedback'></span>" ).insertAfter( $( element ) );
          }
        },
        highlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
          $( element ).next( "span" ).addClass( "glyphicon-remove" ).removeClass( "glyphicon-ok" );
        },
        unhighlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
          $( element ).next( "span" ).addClass( "glyphicon-ok" ).removeClass( "glyphicon-remove" );
        }
  });
  $( "#cmsForm" ).validate( {
        ignore: [],
        rules: {
          title: {
            required: true,
            minlength: 3,
            maxlength: 150,
            atLeastOneLetterpage: true
          },          
          description: {
            ckrequiredpage:true,
          },
          image: {
            accept:"jpg,png,jpeg,gif"
          },
          status: {
            required: true,
          },
        },
        messages: {
          title: {
            required: "Please enter a page name",
            minlength: "Please enter at least {0} characters",
            maxlength: "Please enter no more than {0} characters",
          },          
          description: {
            required: "Please enter page description",
          },
          image: {
            accept: "Only image type jpg/png/jpeg/gif is allowed"
          },
          status: {
            required: "Please select status",
          },
        },
        errorElement: "em",
        errorPlacement: function ( error, element ) {
          // Add the `help-block` class to the error element
          error.addClass( "help-block " );

          // Add `has-feedback` class to the parent div.form-group
          // in order to add icons to inputs
          element.parents( ".col-sm-5" ).addClass( "has-feedback" );

          if ( element.prop( "type" ) === "checkbox" ) {
            error.insertAfter( element.parent( "label" ) );
          } else if ( element.prop( "type" ) === "textarea" ) {
            error.insertAfter( element.parent( "div" ) );
          } else {
            error.insertAfter( element );
          }

          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !element.next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-remove form-control-feedback'></span>" ).insertAfter( element );
          }
        },
        success: function ( label, element ) {
          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !$( element ).next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-ok form-control-feedback'></span>" ).insertAfter( $( element ) );
          }
        },
        highlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
          $( element ).next( "span" ).addClass( "glyphicon-remove" ).removeClass( "glyphicon-ok" );
        },
        unhighlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
          $( element ).next( "span" ).addClass( "glyphicon-ok" ).removeClass( "glyphicon-remove" );
        }
  });
  $( "#planForm" ).validate( {
        ignore: [],
        rules: {
          name: {
            required: true,
            minlength: 3,
            maxlength: 150,
            atLeastOneLetterplan: true
          },          
          feature: {
            ckrequiredplan:true,
          },
          price: {
            required: true,
          },
          status: {
            required: true,
          },
        },
        messages: {
          name: {
            required: "Please enter a plan name",
            minlength: "Please enter at least {0} characters",
            maxlength: "Please enter no more than {0} characters",
          },          
          feature: {
            required: "Please enter plan feature",
          },
          price: {
            required: "Please enter a price",
          },
          status: {
            required: "Please select status",
          },
        },
        errorElement: "em",
        errorPlacement: function ( error, element ) {
          // Add the `help-block` class to the error element
          error.addClass( "help-block " );

          // Add `has-feedback` class to the parent div.form-group
          // in order to add icons to inputs
          element.parents( ".col-sm-5" ).addClass( "has-feedback" );

          if ( element.prop( "type" ) === "checkbox" ) {
            error.insertAfter( element.parent( "label" ) );
          } else if ( element.prop( "type" ) === "textarea" ) {
            error.insertAfter( element.parent( "div" ) );
          } else {
            error.insertAfter( element );
          }

          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !element.next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-remove form-control-feedback'></span>" ).insertAfter( element );
          }
        },
        success: function ( label, element ) {
          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !$( element ).next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-ok form-control-feedback'></span>" ).insertAfter( $( element ) );
          }
        },
        highlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
          $( element ).next( "span" ).addClass( "glyphicon-remove" ).removeClass( "glyphicon-ok" );
        },
        unhighlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
          $( element ).next( "span" ).addClass( "glyphicon-ok" ).removeClass( "glyphicon-remove" );
        }
  });
  $( "#attrSetForm" ).validate( {
        ignore: [],
        rules: {
          name: {
            required: true,
            minlength: 3,
            maxlength: 150,
            atLeastOneLetterattr: true
          },
          'attribute_id[]': {
            required: true,
          },
          attr_type: {
            required: true,
          },
          status: {
            required: true,
          },
        },
        messages: {
          name: {
            required: "Please enter a attribute set name",
            minlength: "Please enter at least {0} characters",
            maxlength: "Please enter no more than {0} characters",
          }, 
          'attribute_id[]': {
            required: "Please enter a attribute",
          },
          attr_type: {
            required: "Please select type",
          },
          status: {
            required: "Please select status",
          },
        },
        errorElement: "em",
        errorPlacement: function ( error, element ) {
          // Add the `help-block` class to the error element
          error.addClass( "help-block " );

          // Add `has-feedback` class to the parent div.form-group
          // in order to add icons to inputs
          element.parents( ".col-sm-5" ).addClass( "has-feedback" );

          if ( element.prop( "type" ) === "checkbox" ) {
            error.insertAfter( element.parent( "label" ) );
          } else if ( element.prop( "type" ) === "textarea" ) {
            error.insertAfter( element.parent( "div" ) );
          } else {
            error.insertAfter( element );
          }

          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !element.next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-remove form-control-feedback'></span>" ).insertAfter( element );
          }
        },
        success: function ( label, element ) {
          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !$( element ).next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-ok form-control-feedback'></span>" ).insertAfter( $( element ) );
          }
        },
        highlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
          $( element ).next( "span" ).addClass( "glyphicon-remove" ).removeClass( "glyphicon-ok" );
        },
        unhighlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
          $( element ).next( "span" ).addClass( "glyphicon-ok" ).removeClass( "glyphicon-remove" );
        }
  });
  $( "#bannerEditForm" ).validate( {
        ignore: [],
        rules: {
          banner_name: {
            required: true,
            minlength: 3,
            maxlength: 150,
            atLeastOneLetter: true
          },          
          banner_description: {
            ckrequired:true,
          },
          role: {
            required: true,
          },
          banner_image: {
            accept:"jpg,png,jpeg,gif"
          },
          status: {
            required: true,
          },
        },
        messages: {
          banner_name: {
            required: "Please enter a banner name",
            minlength: "Please enter at least {0} characters",
            maxlength: "Please enter no more than {0} characters",
          },          
          banner_description: {
            required: "Please enter banner description",
          },
          role: {
            required: "Please select user role",
          },
          banner_image: {
            accept: "Only image type jpg/png/jpeg/gif is allowed"
          },
          status: {
            required: "Please select status",
          },
        },
        errorElement: "em",
        errorPlacement: function ( error, element ) {
          // Add the `help-block` class to the error element
          error.addClass( "help-block " );

          // Add `has-feedback` class to the parent div.form-group
          // in order to add icons to inputs
          element.parents( ".col-sm-5" ).addClass( "has-feedback" );

          if ( element.prop( "type" ) === "checkbox" ) {
            error.insertAfter( element.parent( "label" ) );
          } else if ( element.prop( "type" ) === "textarea" ) {
            error.insertAfter( element.parent( "div" ) );
          } else {
            error.insertAfter( element );
          }

          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !element.next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-remove form-control-feedback'></span>" ).insertAfter( element );
          }
        },
        success: function ( label, element ) {
          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !$( element ).next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-ok form-control-feedback'></span>" ).insertAfter( $( element ) );
          }
        },
        highlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
          $( element ).next( "span" ).addClass( "glyphicon-remove" ).removeClass( "glyphicon-ok" );
        },
        unhighlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
          $( element ).next( "span" ).addClass( "glyphicon-ok" ).removeClass( "glyphicon-remove" );
        }
  });
  $( "#AddProducts" ).validate( {
        ignore: [],
        rules: {
          user_id: {
            required: true
          },
          category_id: {
            required: true
          },
          name: {
            required: true
          },
          sku: {
            required: true,
            noSpace:true,
            minlength: 1,
            maxlength: 50,
            remote: {
                url: $('#baseUrl').val()+'/products/checkSku',
                type: 'post',
                data: {
                    sku: function () {
                        return $('#sku_prefix').val() + '-' + $('#sku').val();
                    }
                },
            }
          },          
          description: {
            ckrequired:true,
          },
          'image[]': {
            accept:"jpg,png,jpeg,gif"
          },
          status: {
            required: true,
          },
        },
        messages: {       
          category_id: {
            required: "Please select category",
          },
          name: {
            required: "Please enter product name",
          },
          sku: {
            required: "Please enter sku",
            minlength: "Please enter at least {0} characters",
            maxlength: "Please enter no more than {0} characters",
            remote: jQuery.validator.format("{0} is already taken.")
          },
          'image[]': {
            accept: "Only image type jpg/png/jpeg/gif is allowed"
          },
          status: {
            required: "Please select status",
          },
        },
        errorElement: "em",
        errorPlacement: function ( error, element ) {
          // Add the `help-block` class to the error element
          error.addClass( "help-block " );

          // Add `has-feedback` class to the parent div.form-group
          // in order to add icons to inputs
          element.parents( ".col-sm-5" ).addClass( "has-feedback" );

          if ( element.prop( "type" ) === "checkbox" ) {
            error.insertAfter( element.parent( "label" ) );
          } else if ( element.prop( "type" ) === "textarea" ) {
            error.insertAfter( element.parent( "div" ) );
          } else {
            error.insertAfter( element );
          }

          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !element.next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-remove form-control-feedback'></span>" ).insertAfter( element );
          }
        },
        success: function ( label, element ) {
          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !$( element ).next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-ok form-control-feedback'></span>" ).insertAfter( $( element ) );
          }
        },
        highlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
          $( element ).next( "span" ).addClass( "glyphicon-remove" ).removeClass( "glyphicon-ok" );
        },
        unhighlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
          $( element ).next( "span" ).addClass( "glyphicon-ok" ).removeClass( "glyphicon-remove" );
        }
  });
  $( "#EditProducts" ).validate( {
        ignore: [],
        rules: {
          user_id: {
            required: true
          },
          category_id: {
            required: true
          },
          /*sub_cat: {
            required: true
          },*/
          name: {
            required: true
          },
		  sku: {
            required: true,
            noSpace:true,
            minlength: 1,
            maxlength: 50,
           /* remote: {
                url: $('#baseUrl').val()+'/products/checkSku',
                type: 'post',
                data: {
                    sku: function () {
                        return $('#sku').val();
                    }
                },
            }*/
          },          
          banner_description: {
            ckrequired:true,
          },
          role: {
            required: true,
          },
          'image[]': {
            accept:"jpg,png,jpeg,gif"
          },
          status: {
            required: true,
          },
        },
        messages: {
          user_id: {
            required: "Please select seller",
          },          
          category_id: {
            required: "Please select category",
          },
          /*sub_cat: {
            required: "Please select sub-category",
          },*/
          name: {
            required: "Please enter product name",
          },
          sku: {
            required: "Please enter sku",
            minlength: "Please enter at least {0} characters",
            maxlength: "Please enter no more than {0} characters",
            /*remote: jQuery.validator.format("{0} is already taken.")*/
          },
          'image[]': {
            accept: "Only image type jpg/png/jpeg/gif is allowed"
          },
          status: {
            required: "Please select status",
          },
        },
        errorElement: "em",
        errorPlacement: function ( error, element ) {
          // Add the `help-block` class to the error element
          error.addClass( "help-block " );

          // Add `has-feedback` class to the parent div.form-group
          // in order to add icons to inputs
          element.parents( ".col-sm-5" ).addClass( "has-feedback" );

          if ( element.prop( "type" ) === "checkbox" ) {
            error.insertAfter( element.parent( "label" ) );
          } else if ( element.prop( "type" ) === "textarea" ) {
            error.insertAfter( element.parent( "div" ) );
          } else {
            error.insertAfter( element );
          }

          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !element.next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-remove form-control-feedback'></span>" ).insertAfter( element );
          }
        },
        success: function ( label, element ) {
          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !$( element ).next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-ok form-control-feedback'></span>" ).insertAfter( $( element ) );
          }
        },
        highlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
          $( element ).next( "span" ).addClass( "glyphicon-remove" ).removeClass( "glyphicon-ok" );
        },
        unhighlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
          $( element ).next( "span" ).addClass( "glyphicon-ok" ).removeClass( "glyphicon-remove" );
        }
  });
  $( "#UserForm" ).validate({
        ignore: [],
        rules: {
          name: {
            required: true,
            minlength: 3,
            maxlength: 50,
            atLeastOneLetteruser: true
          },  
          image: {
            accept:"jpg,png,jpeg,gif"
          },
        },
        messages: {
          name: {
            required: "Please enter a user name",
            minlength: "Please enter at least {0} characters",
            maxlength: "Please enter no more than {0} characters",
          },
          image: {
            accept: "Only image type jpg/png/jpeg/gif is allowed"
          },
        },
        errorElement: "em",
        errorPlacement: function ( error, element ) {
          // Add the `help-block` class to the error element
          error.addClass( "help-block " );

          // Add `has-feedback` class to the parent div.form-group
          // in order to add icons to inputs
          element.parents( ".col-sm-5" ).addClass( "has-feedback" );

          if ( element.prop( "type" ) === "checkbox" ) {
            error.insertAfter( element.parent( "label" ) );
          } else if ( element.prop( "type" ) === "textarea" ) {
            error.insertAfter( element.parent( "div" ) );
          } else {
            error.insertAfter( element );
          }

          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !element.next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-remove form-control-feedback'></span>" ).insertAfter( element );
          }
        },
        success: function ( label, element ) {
          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !$( element ).next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-ok form-control-feedback'></span>" ).insertAfter( $( element ) );
          }
        },
        highlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
          $( element ).next( "span" ).addClass( "glyphicon-remove" ).removeClass( "glyphicon-ok" );
        },
        unhighlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
          $( element ).next( "span" ).addClass( "glyphicon-ok" ).removeClass( "glyphicon-remove" );
        }
  });
  $( "#catForm" ).validate( {
        ignore: [],
        rules: {
          name: {
            required: true,
            minlength: 3,
            maxlength: 150,
            atLeastOneLettercatName: true
          }, 
          image: {
            required: true,
            accept:"jpg,png,jpeg,gif"
          },
          status: {
            required: true,
          },
          parent_id: {
          //  required: true,
          },
        },
        messages: {
          name: {
            required: "Please enter a category name",
            minlength: "Please enter at least {0} characters",
            maxlength: "Please enter no more than {0} characters",
          },
          image: {
            required: "Please select category image",
            accept: "Only image type jpg/png/jpeg/gif is allowed"
          },
          status: {
            required: "Please select status",
          },
          parent_id: {
            required: "Please select Parent Category",
          },
        },
        errorElement: "em",
        errorPlacement: function ( error, element ) {
          // Add the `help-block` class to the error element
          error.addClass( "help-block " );

          // Add `has-feedback` class to the parent div.form-group
          // in order to add icons to inputs
          element.parents( ".col-sm-5" ).addClass( "has-feedback" );

          if ( element.prop( "type" ) === "checkbox" ) {
            error.insertAfter( element.parent( "label" ) );
          } else if ( element.prop( "type" ) === "textarea" ) {
            error.insertAfter( element.parent( "div" ) );
          } else {
            error.insertAfter( element );
          }

          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !element.next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-remove form-control-feedback'></span>" ).insertAfter( element );
          }
        },
        success: function ( label, element ) {
          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !$( element ).next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-ok form-control-feedback'></span>" ).insertAfter( $( element ) );
          }
        },
        highlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
          $( element ).next( "span" ).addClass( "glyphicon-remove" ).removeClass( "glyphicon-ok" );
        },
        unhighlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
          $( element ).next( "span" ).addClass( "glyphicon-ok" ).removeClass( "glyphicon-remove" );
        }
  });
  $( "#catEditForm" ).validate( {
        ignore: [],
        rules: {
          name: {
            required: true,
            minlength: 3,
            maxlength: 150,
            atLeastOneLettercatName: true
          }, 
          image: {
            accept:"jpg,png,jpeg,gif"
          },
          status: {
            required: true,
          },
        },
        messages: {
          name: {
            required: "Please enter a category name",
            minlength: "Please enter at least {0} characters",
            maxlength: "Please enter no more than {0} characters",
          },
          image: {
            accept: "Only image type jpg/png/jpeg/gif is allowed"
          },
          status: {
            required: "Please select status",
          },
        },
        errorElement: "em",
        errorPlacement: function ( error, element ) {
          // Add the `help-block` class to the error element
          error.addClass( "help-block " );

          // Add `has-feedback` class to the parent div.form-group
          // in order to add icons to inputs
          element.parents( ".col-sm-5" ).addClass( "has-feedback" );

          if ( element.prop( "type" ) === "checkbox" ) {
            error.insertAfter( element.parent( "label" ) );
          } else if ( element.prop( "type" ) === "textarea" ) {
            error.insertAfter( element.parent( "div" ) );
          } else {
            error.insertAfter( element );
          }

          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !element.next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-remove form-control-feedback'></span>" ).insertAfter( element );
          }
        },
        success: function ( label, element ) {
          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !$( element ).next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-ok form-control-feedback'></span>" ).insertAfter( $( element ) );
          }
        },
        highlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
          $( element ).next( "span" ).addClass( "glyphicon-remove" ).removeClass( "glyphicon-ok" );
        },
        unhighlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
          $( element ).next( "span" ).addClass( "glyphicon-ok" ).removeClass( "glyphicon-remove" );
        }
  });

});