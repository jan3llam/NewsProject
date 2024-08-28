import { useState, useEffect } from 'react';
import { getNewsFeed, searchArticles, filterArticles } from '../services/apiService';

const useFetchNews = (searchQuery, page = 1, isFilter = false) => {
  const [articles, setArticles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const controller = new AbortController();
    const { signal } = controller;

    const fetchArticles = async (query) => {
      try {
        setLoading(true);
        let response;
        if (query && !isFilter) {
          response = await searchArticles(query, signal, page);
        }
        else if (isFilter) {
          response = await filterArticles(query, signal, page);
        }
        else {
          response = await getNewsFeed(signal);
        }

        if (response.error) {
          setError(response.error);
          setArticles({
            articlesByCategories: [],
            articlesBySources: [],
            articlesByAuthors: [],
          });
        }
        else {
          setArticles(response);
        }
      } catch (error) {
        setError('Failed to fetch articles');
      } finally {
        setLoading(false);
      }
    };

    fetchArticles(searchQuery);

    return () => {
      controller.abort();
    };
  }, [JSON.stringify(searchQuery), page, isFilter]);

  return { articles, loading, error };
};

export default useFetchNews;
