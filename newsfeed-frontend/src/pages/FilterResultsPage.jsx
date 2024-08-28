import React, { useState, useEffect, useMemo } from 'react';
import useFetchNews from '../hooks/useFetchNews';
import FilterResults from '../components/NewsFeed/FilterResults';
import Header from '../components/Header';
import useAuth from '../hooks/useAuth';
import { useNavigate, useLocation } from 'react-router-dom';
import queryString from 'query-string';

const FilterResultsPage = () => {
  const location = useLocation();
  const rawFilters = queryString.parse(location.search);
  const filters = useMemo(() => rawFilters, [location.search]);
  const [currentPage, setCurrentPage] = useState(1);
  const { articles, loading, error } = useFetchNews(filters, currentPage, true);

  const navigate = useNavigate();
  const isAuthenticated = useAuth();

  
  useEffect(() => {
    if (!isAuthenticated) {
      navigate('/login');
    }
  }, [isAuthenticated, navigate]);


  const handlePageChange = (newPage) => {
    setCurrentPage(newPage);
  };


  return (
    <div>
      <Header />
      <main>
        <FilterResults
          data={articles.data}
          loading={loading}
          error={error}
          query={filters.value}
          currentPage={currentPage}
          onPageChange={handlePageChange}
        />
      </main>
    </div>
  );
};

export default FilterResultsPage;
