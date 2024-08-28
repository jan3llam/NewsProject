import React from 'react';
import LoginPage from '../pages/LoginPage';
import RegisterPage from '../pages/RegisterPage';
import NewsFeedPage from '../pages/NewsFeedPage';
import SettingsPage from '../pages/SettingsPage';
import SearchResultsPage from '../pages/SearchResultsPage';
import FilterResultsPage from '../pages/FilterResultsPage';


const RouteConfig = [
  { path: '/login', element: <LoginPage /> },
  { path: '/register', element: <RegisterPage /> },
  { path: '/', element: <NewsFeedPage /> },
  { path: '/news-feed', element: <NewsFeedPage /> },
  { path: '/settings', element: <SettingsPage /> },
  { path: '/search/:query', element: <SearchResultsPage /> },
  { path: '/filter', element: <FilterResultsPage /> },
];

export default RouteConfig;
