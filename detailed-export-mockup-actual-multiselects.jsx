import React, { useState } from 'react';

const columnGroups = {
  'Request Information': [
    { id: 'id', label: 'ID', visible: true },
    { id: 'status', label: 'Status', visible: true },
    { id: 'instruction_type', label: 'Instruction Type', visible: true },
    { id: 'duration', label: 'Duration (Minutes)', visible: true },
    { id: 'number_of_students', label: 'Number of Students', visible: true },
  ],
  'Instructor Information': [
    { id: 'instructor_name', label: 'Instructor Name', visible: true },
    { id: 'instructor_email', label: 'Instructor Email', visible: true },
  ],
  'Course Information': [
    { id: 'campus', label: 'Campus', visible: true },
    { id: 'department', label: 'Department', visible: true },
    { id: 'course_number', label: 'Course Number', visible: true },
    { id: 'course_crn', label: 'Course CRN', visible: false },
    { id: 'class_description', label: 'Class Description', visible: false },
  ],
  'Scheduling': [
    { id: 'instruction_datetime', label: 'Instruction Date/Time', visible: true },
    { id: 'preferred_datetime', label: 'Preferred Date/Time', visible: false },
    { id: 'alternate_datetime', label: 'Alternate Date/Time', visible: false },
    { id: 'async_ready_date', label: 'Async Ready Date', visible: false },
  ],
  'Learning Outcomes': [
    { id: 'assignment_description', label: 'Assignment Description', visible: false },
    { id: 'learning_outcomes', label: 'Learning Outcomes', visible: false },
    { id: 'received_assignment', label: 'Received Assignment', visible: false },
    { id: 'selected_topics', label: 'Selected Topics', visible: false },
    { id: 'explored_background', label: 'Explored Background', visible: false },
    { id: 'written_draft', label: 'Written Draft', visible: false },
  ],
  'Accessibility': [
    { id: 'ada_needed', label: 'ADA Provisions Needed', visible: true },
    { id: 'ada_description', label: 'ADA Description', visible: false },
  ],
  'Librarian': [
    { id: 'assigned_librarian', label: 'Assigned Librarian', visible: true },
    { id: 'requested_librarian', label: 'Requested Librarian', visible: false },
  ],
  'Timestamps': [
    { id: 'created_at', label: 'Created Date', visible: true },
    { id: 'updated_at', label: 'Updated Date', visible: false },
  ],
};

export default function DetailedExportMockup() {
  const [filtersExpanded, setFiltersExpanded] = useState(true);
  const [activeTab, setActiveTab] = useState('institutional');
  const [viewMode, setViewMode] = useState('year');
  const [showColumnModal, setShowColumnModal] = useState(false);
  const [columns, setColumns] = useState(columnGroups);

  const allColumns = Object.values(columns).flat();
  const visibleCount = allColumns.filter(col => col.visible).length;
  const totalCount = allColumns.length;

  const handleMultiSelectChange = (groupName, selectedOptions) => {
    const selectedIds = Array.from(selectedOptions).map(opt => opt.value);
    
    setColumns(prev => ({
      ...prev,
      [groupName]: prev[groupName].map(col => ({
        ...col,
        visible: selectedIds.includes(col.id)
      }))
    }));
  };

  const selectAll = () => {
    setColumns(prev => {
      const updated = {};
      Object.keys(prev).forEach(group => {
        updated[group] = prev[group].map(col => ({ ...col, visible: true }));
      });
      return updated;
    });
  };

  const deselectAll = () => {
    setColumns(prev => {
      const updated = {};
      Object.keys(prev).forEach(group => {
        updated[group] = prev[group].map(col => ({ ...col, visible: false }));
      });
      return updated;
    });
  };

  // Organize groups into 3 columns
  const groupNames = Object.keys(columns);
  const col1Groups = groupNames.slice(0, 3);
  const col2Groups = groupNames.slice(3, 6);
  const col3Groups = groupNames.slice(6);

  return (
    <div className="min-h-screen bg-gray-100 p-8">
      <div className="max-w-7xl mx-auto">
        {/* Page Header */}
        <div className="mb-6">
          <h1 className="text-3xl font-bold text-gray-900">Detailed Export</h1>
          <p className="text-gray-600 mt-1">Export individual session data with customizable columns.</p>
        </div>

        {/* Filter Panel */}
        <div className="mb-6 bg-white rounded-lg shadow border border-gray-200">
          <div className="bg-purple-500 px-4 py-3 rounded-t-lg flex items-center justify-between">
            <h3 className="text-lg font-medium text-white">Report Filters</h3>
            <button
              onClick={() => setFiltersExpanded(!filtersExpanded)}
              className="text-white hover:text-gray-200"
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {filtersExpanded ? (
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 15l7-7 7 7" />
                ) : (
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                )}
              </svg>
            </button>
          </div>

          {filtersExpanded && (
            <div className="p-4">
              {/* Tabs */}
              <div className="mb-4">
                <div className="flex border-b border-gray-200">
                  <button
                    onClick={() => setActiveTab('institutional')}
                    className={`flex items-center gap-2 px-4 py-2 font-medium text-sm transition-all ${
                      activeTab === 'institutional'
                        ? 'text-purple-600 border-b-2 border-purple-600'
                        : 'text-gray-600 hover:text-gray-900'
                    }`}
                  >
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Institutional Periods</span>
                  </button>
                  <button
                    onClick={() => setActiveTab('custom')}
                    className={`flex items-center gap-2 px-4 py-2 font-medium text-sm transition-all ${
                      activeTab === 'custom'
                        ? 'text-purple-600 border-b-2 border-purple-600'
                        : 'text-gray-600 hover:text-gray-900'
                    }`}
                  >
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Custom Range</span>
                  </button>
                </div>

                {/* Tab Content */}
                <div className="mt-4">
                  {activeTab === 'institutional' && (
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">View By</label>
                      <div className="flex gap-2 flex-wrap">
                        {['year', 'fiscal_year', 'term'].map(mode => (
                          <label
                            key={mode}
                            className={`flex items-center px-4 py-2 border rounded-md cursor-pointer ${
                              viewMode === mode
                                ? 'bg-blue-50 border-blue-500'
                                : 'border-gray-300 hover:bg-gray-50'
                            }`}
                          >
                            <input
                              type="radio"
                              checked={viewMode === mode}
                              onChange={() => setViewMode(mode)}
                              className="mr-2"
                            />
                            <span className="text-sm">
                              {mode === 'year' ? 'Academic Year' : mode === 'fiscal_year' ? 'Fiscal Year' : 'Term'}
                            </span>
                          </label>
                        ))}
                      </div>
                    </div>
                  )}
                  {activeTab === 'custom' && (
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">View By</label>
                      <div className="flex gap-2">
                        {['month', 'day'].map(mode => (
                          <label
                            key={mode}
                            className={`flex items-center px-4 py-2 border rounded-md cursor-pointer ${
                              viewMode === mode
                                ? 'bg-blue-50 border-blue-500'
                                : 'border-gray-300 hover:bg-gray-50'
                            }`}
                          >
                            <input
                              type="radio"
                              checked={viewMode === mode}
                              onChange={() => setViewMode(mode)}
                              className="mr-2"
                            />
                            <span className="text-sm">{mode === 'month' ? 'Month' : 'Date Range'}</span>
                          </label>
                        ))}
                      </div>
                    </div>
                  )}
                </div>
              </div>

              {/* Secondary Filters */}
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Campus</label>
                  <select className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white">
                    <option>All Campuses</option>
                    <option>Sylvania</option>
                    <option>Cascade</option>
                    <option>Rock Creek</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Department</label>
                  <input
                    type="text"
                    placeholder="Type to search departments..."
                    className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Class</label>
                  <input
                    type="text"
                    placeholder="Type to search classes..."
                    className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Instructor</label>
                  <input
                    type="text"
                    placeholder="Type to search instructors..."
                    className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Assigned Librarian</label>
                  <select className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white">
                    <option>All Librarians</option>
                  </select>
                </div>
              </div>

              {/* Bottom Row: Clear Filters + Customize Fields */}
              <div className="flex items-center justify-between mt-4">
                <button className="px-4 py-2 text-sm bg-gray-600 text-white rounded hover:bg-gray-700">
                  Clear All Filters
                </button>
                <button
                  onClick={() => setShowColumnModal(true)}
                  className="flex items-center gap-2 text-red-600 hover:text-red-700 font-medium"
                >
                  <span>customize fields</span>
                  <div className="w-6 h-6 border-2 border-red-600 rounded-full flex items-center justify-center">
                    <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                    </svg>
                  </div>
                </button>
              </div>
            </div>
          )}
        </div>

        {/* PowerGrid Table */}
        <div className="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
          {/* Table Header - Green like screenshot */}
          <div className="bg-green-500 px-4 py-3">
            <h3 className="text-lg font-medium text-white">Detailed Export Data</h3>
          </div>
          
          {/* PowerGrid Header with Export Buttons */}
          <div className="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-gray-50">
            <div className="text-sm text-gray-600">
              {visibleCount} of {totalCount} columns visible
            </div>
            <div className="flex gap-2">
              <button className="inline-flex items-center gap-1 px-3 py-1 text-sm border border-gray-300 rounded hover:bg-white">
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                CSV
              </button>
              <button className="inline-flex items-center gap-1 px-3 py-1 text-sm border border-gray-300 rounded hover:bg-white">
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Excel
              </button>
            </div>
          </div>
          
          {/* Table */}
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-100">
                <tr>
                  {allColumns.filter(col => col.visible).map(col => (
                    <th key={col.id} className="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                      {col.label}
                    </th>
                  ))}
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                <tr>
                  {allColumns.filter(col => col.visible).map(col => (
                    <td key={col.id} className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      Sample data
                    </td>
                  ))}
                </tr>
                <tr className="bg-gray-50">
                  {allColumns.filter(col => col.visible).map(col => (
                    <td key={col.id} className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      Sample data
                    </td>
                  ))}
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {/* Column Selection Modal */}
      {showColumnModal && (
        <div 
          className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
          onClick={(e) => {
            if (e.target === e.currentTarget) setShowColumnModal(false);
          }}
        >
          <div className="bg-white rounded-lg shadow-xl max-w-5xl w-full max-h-[85vh] flex flex-col">
            {/* Modal Header */}
            <div className="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
              <div>
                <h2 className="text-lg font-semibold text-gray-900">Customize Columns</h2>
                <p className="text-xs text-gray-500 mt-1">
                  Hold Ctrl/Cmd to select multiple • {visibleCount} of {totalCount} columns selected
                </p>
              </div>
              <button
                onClick={() => setShowColumnModal(false)}
                className="text-gray-400 hover:text-gray-600"
              >
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            {/* Modal Body - 3 Columns of Multiselects */}
            <div className="flex-1 overflow-y-auto p-6">
              {/* Quick Actions */}
              <div className="mb-4 flex justify-end gap-2">
                <button
                  onClick={selectAll}
                  className="px-3 py-1.5 text-xs border border-gray-300 rounded-md hover:bg-gray-50"
                >
                  Select All
                </button>
                <button
                  onClick={deselectAll}
                  className="px-3 py-1.5 text-xs border border-gray-300 rounded-md hover:bg-gray-50"
                >
                  Deselect All
                </button>
              </div>

              {/* 3 Columns of Multiselects */}
              <div className="grid grid-cols-3 gap-4">
                {/* Column 1 */}
                <div className="space-y-4">
                  {col1Groups.map(groupName => (
                    <div key={groupName}>
                      <label className="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">
                        {groupName}
                      </label>
                      <select
                        multiple
                        size={Math.min(columns[groupName].length, 8)}
                        className="w-full border border-gray-300 rounded-md px-2 py-1 text-sm"
                        value={columns[groupName].filter(col => col.visible).map(col => col.id)}
                        onChange={(e) => handleMultiSelectChange(groupName, e.target.selectedOptions)}
                      >
                        {columns[groupName].map(column => (
                          <option key={column.id} value={column.id}>
                            {column.label}
                          </option>
                        ))}
                      </select>
                    </div>
                  ))}
                </div>

                {/* Column 2 */}
                <div className="space-y-4">
                  {col2Groups.map(groupName => (
                    <div key={groupName}>
                      <label className="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">
                        {groupName}
                      </label>
                      <select
                        multiple
                        size={Math.min(columns[groupName].length, 8)}
                        className="w-full border border-gray-300 rounded-md px-2 py-1 text-sm"
                        value={columns[groupName].filter(col => col.visible).map(col => col.id)}
                        onChange={(e) => handleMultiSelectChange(groupName, e.target.selectedOptions)}
                      >
                        {columns[groupName].map(column => (
                          <option key={column.id} value={column.id}>
                            {column.label}
                          </option>
                        ))}
                      </select>
                    </div>
                  ))}
                </div>

                {/* Column 3 */}
                <div className="space-y-4">
                  {col3Groups.map(groupName => (
                    <div key={groupName}>
                      <label className="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">
                        {groupName}
                      </label>
                      <select
                        multiple
                        size={Math.min(columns[groupName].length, 8)}
                        className="w-full border border-gray-300 rounded-md px-2 py-1 text-sm"
                        value={columns[groupName].filter(col => col.visible).map(col => col.id)}
                        onChange={(e) => handleMultiSelectChange(groupName, e.target.selectedOptions)}
                      >
                        {columns[groupName].map(column => (
                          <option key={column.id} value={column.id}>
                            {column.label}
                          </option>
                        ))}
                      </select>
                    </div>
                  ))}
                </div>
              </div>
            </div>

            {/* Modal Footer */}
            <div className="px-6 py-3 border-t border-gray-200 flex justify-end">
              <button
                onClick={() => setShowColumnModal(false)}
                className="px-4 py-2 text-sm bg-purple-600 text-white rounded-md hover:bg-purple-700"
              >
                Apply
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
