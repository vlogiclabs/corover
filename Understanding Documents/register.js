var mongoose = require('mongoose');
var uniqueValidator = require('mongoose-unique-validator');
var Schema = mongoose.Schema;

var registerSchema = new Schema({
    
    token: {
        type: String,
        maxlength: 255,
    },

    phone_number: {
        type: Number,
        maxlength: 255,
       
    },

   countrycode: {
        type: String,
       
    },

     device_id: {
        type: String,
        default: '0',
    },

    username: {
        type: String,
        maxlength: 255,
    },

      company_name: {
        type: String,
        default: 'Company or Organization',
    },

      image_name: {
        type: String,
        maxlength: 255,
    },

      lat: {
            type: String,
        },
    long: {
        type: String,
    },

     location_name: {
        type: String,
        default: '0',

    },

     default_lat: {
            type: String,
             default: '0'
      },

      default_long: {
        type: String,
         default: '0'
      },


    image_path: {
        type: String,
        maxlength: 255,
         default: 'http://13.59.112.113/server/upload/default.png',
    },


    image: {
        type: String,
        maxlength: 255,
         default: 'http://13.59.112.113/server/upload/default.png',
    },


  
     country: {
        type: String,
        maxlength: 255,
    },
     otp: {
        type: String,
        maxlength: 255,
    },
   
    status: {
        type: String,
        maxlength: 255,
        default: 'not_verified'
    },

    token_expired: {
        type: Boolean,
        default: 'No'
    },


  feed_notification: {
        type: Boolean,
        default: 'Yes'
    },


   chat_notification: {
        type: Boolean,
        default: 'Yes'
    },


    created_date: {
        type: Date,
        default: Date.now
    },
    modified_date: {
        type: Date,
        default: Date.now
    }

});


registerSchema.plugin(uniqueValidator,{ message: '{PATH} is to be unique.' });
var register = mongoose.model('register', registerSchema);
module.exports = register;