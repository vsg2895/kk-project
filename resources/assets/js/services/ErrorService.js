import $ from 'jquery';
import _ from 'lodash';

export default class ErrorService {
    static handle(error) {
        if (error.status) {
            if (error.status === 422) {
                this.handleValidationError(error);
            } else if(error.status === 400) {
                this.handleBadRequest(error);
            }
        } else {
            //We don't know what error this is, handle in a generic way
        }
    }

    static handleBadRequest(error) {
        let data = error.body;
        let $container = $('.error-message')[0];
        if ($container) (
            $container.text(data.message)
        )
    }

    static handleValidationError(error) {
        let data = error.body;
        $('.form-control-feedback').remove();
        _.forOwn(data, (value, key) => {
            if (_.includes(key, '.')) {
                let parts = key.split('.');
                key = parts.pop();
            }
            let inputs = $('input[name="'+ key +'"], #'+key);
            _.each(inputs, (input) => {
                let element = $(input);
                if (element.val()) {
                    element.parent().removeClass('has-success').addClass('has-danger');
                    element.addClass('form-control-danger');
                    element.after('<div class="form-control-feedback">' + value + '</div>')
                }
            })
        });
    }
}
