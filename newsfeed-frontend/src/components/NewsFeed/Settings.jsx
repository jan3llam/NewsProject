import React, { useState, useEffect } from 'react';
import { getCategories, getSources, getAuthors, getUserPreferences, updateUserPreferences } from '../../services/apiService';
import PreferenceSection from './PreferencesSection';
import '../../styles/SettingsStyles.css';

const Settings = ({ isAuthenticated, navigate }) => {
    const [categories, setCategories] = useState([]);
    const [sources, setSources] = useState([]);
    const [authors, setAuthors] = useState([]);
    const [categoryPage, setCategoryPage] = useState(1);
    const [sourcePage, setSourcePage] = useState(1);
    const [authorPage, setAuthorPage] = useState(1);
    const [categoryTotalPages, setCategoryTotalPages] = useState(1);
    const [sourceTotalPages, setSourceTotalPages] = useState(1);
    const [authorTotalPages, setAuthorTotalPages] = useState(1);
    const pageSize = 10;
    const [selectedPreferences, setSelectedPreferences] = useState({ categories: [], sources: [], authors: [] });
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(true);
    const [loadingCategory, setLoadingCategory] = useState(false);
    const [loadingSource, setLoadingSource] = useState(false);
    const [loadingAuthor, setLoadingAuthor] = useState(false);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const [categoriesData, sourcesData, authorsData, userPrefsData] = await Promise.all([
                    getCategories(categoryPage, pageSize),
                    getSources(sourcePage, pageSize),
                    getAuthors(authorPage, pageSize),
                    getUserPreferences(),
                ]);

                if (categoriesData.error || sourcesData.error || authorsData.error || userPrefsData.error) {
                    setError('Failed to load data');
                    setLoading(false);
                    return;
                }

                setCategories(categoriesData.data.categories);
                setCategoryTotalPages(categoriesData.data.totalPages);

                setSources(sourcesData.data.sources);
                setSourceTotalPages(sourcesData.data.totalPages);

                setAuthors(authorsData.data.authors);
                setAuthorTotalPages(authorsData.data.totalPages);

                setSelectedPreferences({
                    categories: userPrefsData.data.preferred_categories || [],
                    sources: userPrefsData.data.preferred_sources || [],
                    authors: userPrefsData.data.preferred_authors || [],
                });

                setLoading(false);
            } catch {
                setError('Failed to load data');
                setLoading(false);
            }
        };

        if (!isAuthenticated) {
            navigate('/login');
        } else {
            fetchData();
        }
    }, [isAuthenticated, navigate]);

    const handlePreferenceChange = (type, value) => {
        setSelectedPreferences((prevState) => {
            const updatedSelections = prevState[type].includes(value)
                ? prevState[type].filter((item) => item !== value)
                : [...prevState[type], value];

            return { ...prevState, [type]: updatedSelections };
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            const response = await updateUserPreferences(selectedPreferences);
            if (response.error) {
                setError(response.error);
            } else {
                setError('Preferences updated successfully');
                window.location.reload();
            }
        } catch {
            setError('Failed to update preferences');
        }
    };

    const handlePageChange = async (type, page) => {
        try {
            switch (type) {
                case 'categories':
                    setLoadingCategory(true);
                    setCategories([]); // Reset data to show loading
                    setCategoryPage(page);
                    const categoriesData = await getCategories(page, pageSize);
                    setCategories(categoriesData.data.categories);
                    setLoadingCategory(false);
                    break;
                case 'sources':
                    setLoadingSource(true);
                    setSources([]); // Reset data to show loading
                    setSourcePage(page);
                    const sourcesData = await getSources(page, pageSize);
                    setSources(sourcesData.data.sources);
                    setLoadingSource(false);
                    break;
                case 'authors':
                    setLoadingAuthor(true);
                    setAuthors([]); // Reset data to show loading
                    setAuthorPage(page);
                    const authorsData = await getAuthors(page, pageSize);
                    setAuthors(authorsData.data.authors);
                    setLoadingAuthor(false);
                    break;
                default:
                    break;
            }
        } catch {
            setError('Failed to load data');
            setLoadingCategory(false);
            setLoadingSource(false);
            setLoadingAuthor(false);
        }
    };

    if (loading) return <div className="loading-message">Loading...</div>;
    if (error) return <div className="error-message">{error}</div>;

    return (
        <div className="settings-page">
            <h2>User Preferences</h2>
            <PreferenceSection
                title="Categories"
                options={categories}
                selectedOptions={selectedPreferences.categories}
                onChange={(value) => handlePreferenceChange('categories', value)}
                pagination={{
                    currentPage: categoryPage,
                    totalPages: categoryTotalPages,
                    onNext: () => handlePageChange('categories', Math.min(categoryPage + 1, categoryTotalPages)),
                    onPrev: () => handlePageChange('categories', Math.max(categoryPage - 1, 1)),
                }}
                loading={loadingCategory}
            />
            <PreferenceSection
                title="Sources"
                options={sources}
                selectedOptions={selectedPreferences.sources}
                onChange={(value) => handlePreferenceChange('sources', value)}
                pagination={{
                    currentPage: sourcePage,
                    totalPages: sourceTotalPages,
                    onNext: () => handlePageChange('sources', Math.min(sourcePage + 1, sourceTotalPages)),
                    onPrev: () => handlePageChange('sources', Math.max(sourcePage - 1, 1)),
                }}
                loading={loadingSource}
            />
            <PreferenceSection
                title="Authors"
                options={authors}
                selectedOptions={selectedPreferences.authors}
                onChange={(value) => handlePreferenceChange('authors', value)}
                pagination={{
                    currentPage: authorPage,
                    totalPages: authorTotalPages,
                    onNext: () => handlePageChange('authors', Math.min(authorPage + 1, authorTotalPages)),
                    onPrev: () => handlePageChange('authors', Math.max(authorPage - 1, 1)),
                }}
                loading={loadingAuthor}
            />
            <button className="submit-button" onClick={handleSubmit}>Save Preferences</button>
        </div>
    );
};

export default Settings;
