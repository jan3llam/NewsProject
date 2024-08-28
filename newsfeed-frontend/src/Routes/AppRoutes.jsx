import React from 'react';
import { Routes, Route } from 'react-router-dom';
import RouteConfig from './RouteConfig';

const AppRoutes = () => {
  return (
    <Routes>
      {RouteConfig.map(({ path, element }) => (
        <Route key={path} path={path} element={element} />
      ))}
    </Routes>
  );
};

export default AppRoutes;
