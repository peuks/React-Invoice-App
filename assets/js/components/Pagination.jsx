import React from "react";
//  <Pagination
//         currentPage={currentPage}
//         itemsPerPage={itemsPerPage}
//         length={customers.length}
//         onPagechanged={handlePageChange}
//       />
const Pagination = ({ currentPage, itemPerPage, length, onPageChanged }) => {
  return (
    <div>
      <ul className="pagination pagination-sm">
        <li className={`page-item ${currentPage === 1 && "disabled"}`}>
          <button
            className="page-link"
            onClick={() => {
              handleCangePage(currentPage - 1);
            }}
          >
            &laquo;
          </button>
        </li>
        {pages.map((page) => (
          <li
            key={page}
            // Renvoyer la valeur après le && si la condition est vraie
            className={`page-item  ` + (currentPage === page && "active")}
          >
            <button
              className="page-link"
              onClick={() => {
                handleCangePage(page);
              }}
            >
              {page}
            </button>
          </li>
        ))}
        <li className={`page-item ${currentPage === pageCount && "disabled"}`}>
          <button
            className="page-link"
            onClick={() => {
              handleCangePage(currentPage + 1);
            }}
          >
            &raquo;
          </button>
        </li>
      </ul>
    </div>
  );
};

export default Pagination;
