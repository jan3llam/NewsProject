import api from './axiosConfig';


const handleResponse = (response) => {
  if (response.data.error) {
    throw new Error(response.data.message || 'An error occurred');
  }
  return response.data;
};


const handleError = (error) => {
  console.error('API call error:', error.message);

  if (error.response) {
    if (error.response.data && error.response.data.errors) {
      const errors = error.response.data.errors;
      const errorMessages = Object.values(errors).flat();
      return errorMessages.join(' ');
    } else if (error.response.data && error.response.data.message) {
      return error.response.data.message;
    } else {
      return 'An unknown error occurred';
    }
  } else {
    return error.message || 'Network error';
  }
};


export const login = async (email, password) => {
  try {
    const response = await api.post('/users/auth/login', { email, password });

    return handleResponse(response);
  } catch (error) {
    return { error: handleError(error) };
  }
};

export const register = async (name, email, password, confirmPassword) => {
  try {
    const response = await api.post('/users/auth/register', {
      name,
      email,
      password,
      password_confirmation: confirmPassword
    });
    return handleResponse(response);
  } catch (error) {
    return { error: handleError(error) };
  }
};

export const logout = async () => {
  try {
    const response = await api.post('/users/auth/logout');
    return handleResponse(response);
  } catch (error) {
    return { error: handleError(error) };
  }
};

export const getNewsFeed = async (signal) => {
  try {
    const response = await api.get('/users/news-feed', { signal });
    return handleResponse(response);
  } catch (error) {
    return { error: handleError(error) };
  }
};

export const getUserPreferences = async () => {
  try {
    const response = await api.get('/users/userPreferences');
    return handleResponse(response);
  } catch (error) {
    return { error: handleError(error) };
  }
};

export const updateUserPreferences = async (preferences) => {
  try {
    const formattedPreferences = {
      preferred_categories: preferences.categories,
      preferred_sources: preferences.sources,
      preferred_authors: preferences.authors,
    };

    const response = await api.put('/users/userPreferences', formattedPreferences);
    return handleResponse(response);
  } catch (error) {
    return { error: handleError(error) };
  }
};


export const getCategories = async (page = 1, pageSize = 10) => {
  try {
    const response = await api.get('news/preferences/categories', { params: { page, pageSize } });
    return handleResponse(response);
  } catch (error) {
    return { error: handleError(error) };
  }
};

export const getSources = async (page = 1, pageSize = 10) => {
  try {
    const response = await api.get('news/preferences/sources', { params: { page, pageSize } });
    return handleResponse(response);
  } catch (error) {
    return { error: handleError(error) };
  }
};

export const getAuthors = async (page = 1, pageSize = 10) => {
  try {
    const response = await api.get('news/preferences/authors', { params: { page, pageSize } });
    return handleResponse(response);
  } catch (error) {
    return { error: handleError(error) };
  }
};


export const searchArticles = async (query, signal, page = 1, pageSize = 10) => {
  try {
    const response = await api.post('/news/search', { keyword: query, page, pageSize }, { signal });
    return handleResponse(response);
  } catch (error) {
    return { error: handleError(error) };
  }
};

export const filterArticles = async (filters, signal, page = 1, pageSize = 10) => {
  try {
    const response = await api.post('/news/filter', { filters, page, pageSize }, { signal });
    return handleResponse(response);
  } catch (error) {
    return { error: handleError(error) };
  }
};

