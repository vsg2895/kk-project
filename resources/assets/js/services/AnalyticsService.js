export default class AnalyticsService {
    static event(action) {
        let url = window.location.href;

        mtr.event('url: ' + url + ' ' + action);
    }

    static eventGift(action, gift) {
        let giftStr = this.giftToString(gift);

        this.event(action + ' ' + giftStr);
    }

    static eventCourse(action, course) {
        let courseStr = this.courseToString(course);

        this.event(action + ' ' + courseStr);
    }

    static eventAddon(action, addon) {
        let addonStr = this.addonToString(addon);

        this.event(action + ' ' + addonStr);
    }

    static giftToString(gift) {
        return 'gift:' +
            '{' +
            'id: ' + gift.id + '; ' +
            'name: ' + gift.name +
            '}';
    }

    static courseToString(course) {
        return 'course:' +
            '{' +
            'id: ' + course.id + '; ' +
            'name: ' + course.name +
            '}';
    }

    static addonToString(addon) {
        return 'addon:' +
            '{' +
            'id: ' + addon.id + '; ' +
            'name: ' + addon.name +
            '}';
    }
}
