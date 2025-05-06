export const state = () => ({
  auth: {
    loggedIn: false,
    user: null,
    token: null,
  }
});

export const mutations = {
  SET_AUTH(state, { user, token }) {
    state.auth.loggedIn = true;
    state.auth.user = user;
    state.auth.token = token;
  },
  LOGOUT(state) {
    state.auth.loggedIn = false;
    state.auth.user = null;
    state.auth.token = null;
  }
};

export const actions = {
  login({ commit }, { user, token }) {
    commit('SET_AUTH', { user, token });
    // Save to localStorage
    localStorage.setItem('auth_token', token);
    localStorage.setItem('auth_user', JSON.stringify(user)); // Store user info
  },
  logout({ commit }) {
    commit('LOGOUT');
    // Remove from localStorage
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
  },
  checkAuth({ commit }) {
    // Check if token exists in localStorage
    const token = localStorage.getItem('auth_token');
    const user = JSON.parse(localStorage.getItem('auth_user'));

    if (token && user) {
      // If both token and user are present, commit them to the store
      commit('SET_AUTH', { user, token });
    }
  }
};

export const getters = {
  isAuthenticated(state) {
    return state.auth.loggedIn;
  },
  getUser(state) {
    return state.auth.user;
  }
};
