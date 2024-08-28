import React from 'react';
import useFetchNews from '../../hooks/useFetchNews';
import ArticleCard from './ArticleCard';
import '../../styles/NewsFeedStyles.css';

const NewsFeed = () => {
  const { articles, loading, error } = useFetchNews();

  if (loading) return <div className="loading-message">Loading...</div>;
  if (error) return <div className="error-message">{error}</div>;

  return (
    <div className="news-feed">
      {articles?.data?.length === 0 && (
        <div className="no-articles-message">
          {articles.message}
        </div>
      )}
      {articles?.data?.articlesByCategories?.length > 0 && (
        <section className="news-section">
          <h2 className="section-title">Articles by Categories</h2>
          {articles.data.articlesByCategories.map((article, index) => (
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
        </section>
      )}
      {articles?.data?.articlesByCategories?.length === 0 && (
        <div className="no-articles-message">
          No articles available for your preferred categories.
        </div>
      )}

      {articles?.data?.articlesBySources?.length > 0 && (
        <section className="news-section">
          <h2 className="section-title">Articles by Sources</h2>
          {articles.data.articlesBySources.map((article, index) => (
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
        </section>
      )}
      {articles?.data?.articlesBySources?.length === 0 && (
        <div className="no-articles-message">
          No articles available from your preferred sources.
        </div>
      )}

      {articles?.data?.articlesByAuthors?.length > 0 && (
        <section className="news-section">
          <h2 className="section-title">Articles by Authors</h2>
          {articles.data.articlesByAuthors.map((article, index) => (
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
        </section>
      )}
      {articles?.data?.articlesByAuthors?.length === 0 && (
        <div className="no-articles-message">
          No articles available from your preferred authors.
        </div>
      )}
    </div>
  );
};

export default NewsFeed;
