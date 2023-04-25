const ADDON_TYPE = 'addon';
const CUSTOM_ADDON_TYPE = 'custom_addon';
const FALSY_TYPES = [NaN, 'NaN', undefined, 'undefined', null, 'null', ''];
const isFalsy = (val) => FALSY_TYPES.includes(val);
const setState = (name, defaultValue) => isFalsy(localStorage.getItem(name))
    ? defaultValue
    : JSON.parse(localStorage.getItem(name));

const getters = {
  qty(state) {
    return state.courses.length + state.addons.length;
  },
  courseIds(state) {
    return state.courses.map(c => c.id);
  },
  addonIds(state) {
    return state.addons.filter(a => a.type === ADDON_TYPE).map(a => a.id);
  },
  customIds(state) {
    return state.addons.filter(a => a.type === CUSTOM_ADDON_TYPE).map(a => a.id);
  },
};

const actions = {
  addCourse({ commit, dispatch }, course) {
    commit('addCourse', course);
    dispatch('addStudents', { courseId: course.id, length: course.selectedSeats });
  },
  updateCourse({ commit, dispatch }, course) {
    commit('updateCourse', course);
    dispatch('addStudents', { courseId: course.id, length: course.selectedSeats });
  },
  upsertCourse({ commit, dispatch }, course) {
    commit('upsertCourse', course);
    dispatch('addStudents', { courseId: course.id, length: course.selectedSeats });
  },
  removeCourse({ commit }, course) {
    commit('removeCourse', course);
    commit('removeCourseStudents', course.id);
  },
  addAddon({ commit }, addon) {
    commit('addAddon', addon);
  },
  removeAddon({ commit }, addon) {
    commit('removeAddon', addon);
  },
  clear({ commit }) {
    commit('setCourses', []);
    commit('setAddons', []);
    commit('setStudents', []);
  },
  addStudents({ commit }, { courseId, length }) {
    const students = Array.from({ length }, () => ({
      given_name: '',
      family_name: '',
      social_security_number: '',
      email: '',
      transmission: '',
      courseId,
    }));
    commit('addStudents', students);
  },
};

const mutations = {
  setCourses(state, courses) {
    state.courses = courses;
    localStorage.setItem('selectedCourses', JSON.stringify(courses));
  },
  upsertCourse(state, course) {
    const index = state.courses.findIndex(({ id }) => id === course.id);

    if (index !== -1) {
      const updated = { ...state.courses[index], ...course };
  
      state.courses = [updated, ...state.courses.filter((a, i) => i !== index)];
    } else {
      state.courses.push(course);
    }
  
    localStorage.setItem('selectedCourses', JSON.stringify(state.courses));
  },
  removeCourse(state, course) {
    state.courses = state.courses.filter((c) => c.id !== course.id);
    localStorage.setItem('selectedCourses', JSON.stringify(state.courses));
  },
  removeCourseStudents(state, courseId) {
    state.students = state.students.filter((s) => s.courseId !== courseId);
    localStorage.setItem('students', JSON.stringify(state.students));
  },
  setAddons(state, addons) {
    state.addons = addons;
    localStorage.setItem('selectedAddons', JSON.stringify(addons));
  },
  addAddon(state, addon) {
    state.addons.push(addon);
    localStorage.setItem('selectedAddons', JSON.stringify(state.addons));
  },
  removeAddon(state, addon) {
    state.addons = state.addons.filter((a) => a.id !== addon.id);
    localStorage.setItem('selectedAddons', JSON.stringify(state.addons));
  },
  setStudents(state, students) {
    state.students = students;
    localStorage.setItem('students', JSON.stringify(students));
  },
  addStudents(state, students) {
    state.students = state.students.concat(students);
    localStorage.setItem('students', JSON.stringify(state.students));
  },
};

export default {
  namespaced: true,
  getters,
  actions,
  mutations,
  state: {
    courses: setState('selectedCourses', []),
    addons: setState('selectedAddons', []),
    students: setState('students', []),
  },
};
