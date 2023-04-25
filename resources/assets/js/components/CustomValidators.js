import { withParams } from 'vuelidate/lib';

const socialSecurityRegex = new RegExp('^(19|20)?[0-9]{2}[- ]?((0[0-9])|(10|11|12))[- ]?(([0-2][0-9])|(3[0-1])|(([7-8][0-9])|(6[1-9])|(9[0-1])))[- ]?[0-9]{4}$');
const organizationSecurityRegex = new RegExp(/^(\d{2})([2-9])(\d{3})\-?(\d{4})$/);

export const socialSecurityNumber = withParams({type: 'required'}, value => {
    return socialSecurityRegex.test(value);
});

export const organizationSecurityNumber = withParams({type: 'required'}, value => {
    return organizationSecurityRegex.test(value) || socialSecurityRegex.test(value);
});


export default { socialSecurityNumber, organizationSecurityNumber }