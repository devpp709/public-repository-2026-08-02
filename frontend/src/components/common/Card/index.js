import React from 'react';

const Card = ({ children, className = '', title, ...props }) => {
  return (
    <div
      className={\g-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 \\}
      {...props}
    >
      {title && (
        <div className="px-6 py-4 border-b border-gray-200">
          <h3 className="text-lg font-semibold text-gray-900">{title}</h3>
        </div>
      )}
      <div className="p-6">{children}</div>
    </div>
  );
};

export default Card;
