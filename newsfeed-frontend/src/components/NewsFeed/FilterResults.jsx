import React from 'react';
import ArticleCard from './ArticleCard';
import '../../styles/NewsFeedStyles.css';

const FilterResults = ({ data, loading, error, query, currentPage, onPageChange }) => {
  if (loading) return <div className="loading-message">Loading...</div>;
  if (error) return <div className="error-message">{error}</div>;

  const articleList = data.articles || [];
  const totalPages = data.totalPages || 1;

  return (
    <div className="news-feed">
      {articleList.length > 0 ? (
        <>
          {articleList.map((article, index) => (
            <ArticleCard
              key={index}
              title={article.title}
              description={article.description}
              url={article.url}
              source={article.source}
              author={article.author}
              image={article.image}
              publishedAt={article.publishedAt}
            />
          ))}
          <div className="pagination-controls">
            <button
              onClick={() => onPageChange(Math.max(currentPage - 1, 1))}
              disabled={currentPage === 1}
            >
              Previous
            </button>
            <span>{currentPage} / {totalPages}</span>
            <button
              onClick={() => onPageChange(Math.min(currentPage + 1, totalPages))}
              disabled={currentPage === totalPages}
            >
              Next
            </button>
          </div>
        </>
      ) : (
        <div className="no-articles-message">
          No articles found for "{query}".
        </div>
      )}
    </div>
  );
};

export default FilterResults;
