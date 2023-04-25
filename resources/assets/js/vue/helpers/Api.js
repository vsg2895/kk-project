import Vue from 'vue';
import VueResource from 'vue-resource';
import routes from 'build/routes.js';
import ErrorService from 'services/ErrorService';
import $ from 'jquery';

Vue.use(VueResource);

export default class Api {
    static searchCourses(filter) {
        return Vue.http.get(routes.route('api::courses.search', filter)).then(function (response) {
            let resp = response.body;
            return {
                booted: resp.courses.length,
                data: resp
            };
        }, function (error) {
            return error;
        });
    }

    static searchSchools(filter) {
        return Vue.http.get(routes.route('api::schools.search', filter)).then(function (response) {
            let resp = response.body;
            return {
                booted: resp.schools.length,
                data: resp
            };
        }, function (error) {
            return error;
        });
    }

    static giftCardSchools(cityId) {
        return Vue.http.get(routes.route('api::schools.acceptsGiftCard', {cityId}))
            .then(response => response.body)
            .catch(err => err);
    }

    static getCities() {
        return Vue.http.get(routes.route('api::cities.all')).then(function (response) {
            return response.body;
        }, function (error) {
            return error;
        });
    }

    static getSchools() {
        return Vue.http.get(routes.route('api::schools.all')).then(function (response) {
            return response.body;
        }, function (error) {
            ErrorService.handle(error);
        });
    }

    static getSchoolLoyaltyLevel(id) {
        return Vue.http.get(routes.route('api::schools.getLoyaltyLevel', { id })).then(function (response) {
            return response.body;
        }, function (error) {
            ErrorService.handle(error);
        });
    }

    static getSchoolsForLoggedInUser() {
        return Vue.http.get(routes.route('api::schools.user')).then(function (response) {
            return response.body;
        }, function (error) {
            ErrorService.handle(error);
        });
    }

    static findSchool(id) {
        return Vue.http.get(routes.route('api::schools.find', {id: id})).then(function (response) {
            return response.body;
        }, function (error) {
            return error;
        });
    }

    static storeSchool(school) {
        return Vue.http.post(routes.route('api::schools.store'), school).then(function (response) {
            return response.body;
        }, function (error) {
            return error;
        });
    }

    static getVehicleTypes() {
        return Vue.http.get(routes.route('api::vehicles.all')).then(function (response) {
            return response.body;
        }, function (error) {
            return error;
        });
    }

    static getVehicleTypesForSchool(id) {
        return Vue.http.get(routes.route('api::vehicles.school', {id: id})).then(function (response) {
            return response.body;
        }, function (error) {
            return error;
        });
    }

    static getVehicleSegmentsOrder(id) {
        return Vue.http.get(routes.route('api::vehicles.school.order', {id: id})).then(function (response) {
            return response.body;
        }, function (error) {
            return error;
        });
    }

    static getVehicleSegments(bookable) {
        return Vue.http.get(routes.route('api::vehicle_segments.all', {bookable: bookable ? 1 : 0})).then(function (response) {
            return response.body;
        }, function (error) {
            return error;
        });
    }

    static getVehicleSegmentsForSchool(id) {
        return Vue.http.get(routes.route('api::vehicle_segments.school', {id: id})).then(function (response) {
            return response.body;
        }, function (error) {
            return error;
        });
    }

    static findCourse(id) {
        return Vue.http.get(routes.route('api::courses.find', {id: id})).then(function (response) {
            return response.body;
        }, function (error) {
            return error;
        });
    }

    static getLoggedInUser() {
        return Vue.http.get(routes.route('api::auth.user')).then(function (response) {
            return response.body;
        }, function (error) {
            return error;
        });
    }

    static logIn(credentials) {
        return Vue.http.post(routes.route('api::auth.login'), credentials).then(function (response) {
            return response.body;
        }, function (error) {
            ErrorService.handle(error);
        })
    }

    static storeOrder(order) {
        let courseId;
        if (order.course) {
            courseId = order.course.id;
        } else {
            courseId = order.courses.map(c => c.id).join(',');
        }
        return Vue.http.post(routes.route('api::orders.store', {courseId}), order).then(function (response) {
            return response.body;
        }, function (error) {
            ErrorService.handle(error);
        });
    }

    static pendingExternarOrder(order) {
        // TODO: As createKlarnaOrder works only with one id
        return Vue.http.post(routes.route('api::klarna.store', {courseId: order.course.id}), order).then(function (response) {
            return response.body;
        }, function (error) {
            // handle 'KLARNA FAILED TO CREATE ORDER'
            ErrorService.handle(error);
        });

    }

    static createKlarnaOrder(courseId, participants, addons) {
        //TODO: Not used? Remove? We are using multiple course ids now
        return Vue.http.post(routes.route('api::klarna.store', {courseId: courseId}), {
            participants: participants,
            addons: addons
        }).then(function (response) {
            return response.body;
        }, function (error) {
            // handle 'KLARNA FAILED TO CREATE ORDER'
            ErrorService.handle(error);
        });
    }

    static getKlarnaOrder(orderId) {
        return Vue.http.get(routes.route('api::klarna.find', {id: orderId})).then(function (response) {
            return response.body;
        }, function (error) {
            // handle 'KLARNA FAILED TO FETCH ORDER', error
            return error;
        });
    }

    static updateKlarnaOrder(orderId, data) {
        Vue.http.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');
        return Vue.http.post(routes.route('api::klarna.update', {orderId: orderId}), data).then(function (response) {
            return response.body;
        }, function (error) {
            // handle 'KLARNA FAILED TO UPDATE ORDER'
            return error;
        });
    }

    static initiateKlarnaOnboarding() {
        return Vue.http.post(routes.route('api::klarna.onboarding.initiate')).then(function (response) {
            return response;
        }, function (error) {
            // handle 'KLARNA FAILED TO INITIATE ONBOARDING'
            return error;
        });
    }

    static contactStore(contactForm) {
        return Vue.http.post(routes.route('api::contact.store'), contactForm).then(function (response) {
            return response.body;
        }, function (error) {
            ErrorService.handle(error);
        });
    }

    static getUserRatingForSchool(schoolId) {
        return Vue.http.get(routes.route('api::schools.user_rating', {id: schoolId})).then(function (response) {
            return response.body;
        }, function (error) {
            // handle 'FAILED TO FIND SCHOOl RATING BY USER', error
            return error;
        });
    }

    static rateSchool(school, rating) {
        return Vue.http.post(routes.route('api::schools.rate', {id: school.id}), {
            rating: rating.rating,
            title: rating.title,
            content: rating.content
        }).then(function (response) {
            return response.body;
        }, function (error) {
            // handle 'FAILED TO RATE SCHOOl', error
            return error;
        });
    }

    static getGiftCardTypes() {
        return Vue.http.get(routes.route('api::giftcard.type.index')).then(function (response) {
            return response.body;
        }, function (error) {
            return error;
        });
    }

    static checkGiftCard(token, courseId = null, hasMopedAm = null) {
        return Vue.http.post(routes.route('api::giftcard.check'), {token, courseId, hasMopedAm}).then(function (response) {
            return response.body;
        }, function (error) {
            return error;
        });
    }

    static deleteRateForSchool(school) {
        return Vue.http.delete(routes.route('api::schools.delete_rate', {id: school.id})).then(function (response) {
            return response.body;
        }, function (error) {
            // handle 'FAILED TO DELETE RATE FOR SCHOOl', error
            return error;
        });
    }

    static claimSchool(school) {
        return Vue.http.post(routes.route('api::schools.claim', {id: school.id})).then(function (response) {
            return response.body;
        }, function (error) {
            // handle 'FAILED TO CLAIM SCHOOl', error
            return error;
        });
    }

    static findOrder(orderId) {
        return Vue.http.get(routes.route('api::orders.find', {id: orderId})).then(function (response) {
            return response.body;
        }, function (error) {
            return error;
        });
    }

    static updateCourse(data) {
        return Vue.http.post(routes.route('api::courses.update', {id: data.id}), {
            seats: data.seats,
            price: data.price,
            start_time: data.start_time
        }).then(function (response) {
            return response.body;
        }, function (error) {
            return error;
        });
    }

    static updateOrder(order) {
        return Vue.http.post(routes.route('api::orders.update', {id: order.id}), {
            items: order.items,
            invoice_sent: order.invoice_sent
        }).then(function (response) {
            return response.body;
        }, function (error) {
            return error;
        });
    }

    static cancelOrder(order) {
        return Vue.http.post(routes.route('api::orders.cancel', {id: order.id})).then(function (response) {
            return response.body;
        }, function (error) {
            return error;
        });
    }

    static topRegions(id) {
        let url = '/api/statistics/topFive';
        url += id ? '/' + id : '';
        return Vue.http.get(url);
    }

    static orderStatistics(routeData) {
        let route = [];
        route.push('/api/statistics/orders');
        route.push('/' + routeData.startDate);
        route.push('/' + routeData.endDate);
        route.push('/' + routeData.granularity);
        route.push('/' + routeData.type);
        route.push('/' + routeData.cityId);
        route.push('/' + routeData.orgId);
        route.push('/' + routeData.schoolId);
        let url = route.join('');
        return Vue.http.get(url);
    }

// Custom Filter Request Logic
    static customOrganizationFilter(currentValue) {
        let url = window.location.origin + '/organization/statistics/' + currentValue;
        return Vue.http.get(url);
    }

    static toCheckout(schoolId, courseIds, addonIds, customIds, students = [], iframe = false) {
        //TODO: This should be a post request since it actually creates a pending order
        let route = [];
        route.push('/betalningssida?skola=' + schoolId);

        if (courseIds && courseIds.length) {
            route.push('&kurser=');
            courseIds.forEach((courseId, i) => {
                i === courseIds.length - 1 ? route.push(courseId) : route.push(courseId + ',');
            });
        }
        if (addonIds && addonIds.length) {
            route.push('&tillagg=');
            addonIds.forEach((addonId, i) => {
                i === addonIds.length - 1 ? route.push(addonId) : route.push(addonId + ',');
            });
        }
        if (customIds && customIds.length) {
            route.push('&paket=');
            customIds.forEach((customId, i) => {
                i === customIds.length - 1 ? route.push(customId) : route.push(customId + ',');
            });
        }
        if (iframe) {
            route.push('&iframe=true');
        }
        let url = route.join('');

        if (students && students.length) {
            let formatedStudents = [];

            students.forEach((student) => {
                if (!formatedStudents[student.courseId]) {
                    formatedStudents[student.courseId] = 1;
                } else {
                    formatedStudents[student.courseId]++;
                }
            });

            formatedStudents.forEach((qty, id) => {
                url += '&' + id + '=' + qty
            });
        }
        window.location = url;
    }

    static toCheckout2(giftCardTypeIds) {
        let requestBody = {
            giftCardTypeIds: giftCardTypeIds,
        };

        return Vue.http.post(routes.route('shared::payment2.store', requestBody))
            .then(function (response) {
                //Redirect to check out index page
                let result = response.body;
                window.location = result.checkoutPage;
                return response.body
            }, function (error) {
                let msg = "Något blev fel när ordern skulle skapas, beställning ej utförd. "
                    + "Kontakta support om felet kvarstår.";
                alert(msg)
                return error
            });
    }

    static findPost(postId) {
        return Vue.http.get(routes.route('api::blog.posts.find', {post: postId})).then(function (response) {
            return response.body;
        }, function (error) {
            return error;
        });
    }

    static searchPosts(filter) {
        return Vue.http.get(routes.route('api::blog.posts.search', filter)).then(function (response) {
            let resp = response.body;
            return {
                booted: resp.posts.length,
                data: resp
            };
        }, function (error) {
            return error;
        });
    }

    static searchComments(filter) {
        return Vue.http.get(routes.route('api::blog.comments.search', filter)).then(function (response) {
            let resp = response.body;
            return {
                booted: resp.comments.length,
                data: resp
            };
        }, function (error) {
            return error;
        });
    }

    static activateOrCancelOrder(orderId, param) {
        return Vue.http.post(`/api/orders/${orderId}/klarna`, {...param})
            .then(response => response.body, error => error);
    }
}
