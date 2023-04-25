import _ from 'lodash';
import Api from 'vue-helpers/Api';

export default class Klarna {
    static setupOrder(order) {
        let klarnaOrder = {
            "purchase_country": "SE",
            "purchase_currency": "SEK",
            "locale": "sv-SE",
            "order_lines": [],
            "merchant_urls": {
                "terms": "https://www.estore.com/terms.html",
                "checkout": "https://www.estore.com/checkout.html",
                "confirmation": "https://www.estore.com/confirmation.html",
                "push": "https://www.estore.com/api/push",
                "validation": "https://www.estore.com/api/validation",
                "shipping_option_update": "https://www.estore.com/api/shipment",
                "address_update": "https://www.estore.com/api/address",
                "notification": "https://www.estore.com/api/pending",
                "country_change": "https://www.estore.com/api/country"
            }
        };

        klarnaOrder = this.setOrderLines(klarnaOrder, order);

        return klarnaOrder;
    }

    static setOrderLines(klarnaOrder, order) {
        if (order.course) {
            let tax = 25;
            let quantity = order.students.length + order.tutors.length;
            let totalAmount = quantity * order.course.price;
            klarnaOrder.order_lines.push({
                name: 'Riskettan',
                reference: '1',
                type: 'physical',
                quantity: quantity,
                quantity_unit: 'personer',
                unit_price: order.course.price,
                tax_rate: tax * 1000, //25%,
                total_amount: totalAmount,
                total_tax_amount: (tax / 100) * totalAmount,
                merchant_data: JSON.stringify({data: 'hej'})
            });
        }

        _.forEach(order.addons, (addon) => {
            let tax = 12;
            klarnaOrder.order_lines.push({
                name: addon.name,
                reference: 'addon-1',
                type: 'physical',
                quantity: 1,
                quantity_unit: 'st',
                unit_price: addon.price,
                tax_rate: tax * 1000, //25%,
                total_amount: addon.price,
                total_tax_amount: (tax / 100) * addon.price,
                merchant_data: JSON.stringify({data: 'addon'})
            });
        });

        klarnaOrder.order_amount = _.reduce(klarnaOrder.order_lines, (total, line, key) => {
            return total + line.total_amount;
        }, 0);

        klarnaOrder.order_tax_amount = _.reduce(klarnaOrder.order_lines, (total, line, key) => {
            return total + line.total_tax_amount;
        }, 0);

        return klarnaOrder;
    }

    static createOrder(courseId, participants, addons) {
        return Api.createKlarnaOrder(courseId, participants, addons);
    }

    static getOrder(orderId) {
        return Api.getKlarnaOrder(orderId);
    }

    static updateOrder(orderId, data) {
        return Api.updateKlarnaOrder(orderId, data);
    }
}
