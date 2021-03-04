import React from "react";
{
  /* <Pagination
        currentPage={currentPage}
        itemsPerPage={itemsPerPage}
        length={cutomers.length}
        onPageChanged={handlePageChange}
      /> */
}
const Pagination = ({ currentPage, itemsPerPage, length, onPageChanged }) => {
  // Arrondir à l'entier supérieur
  const pageCount = Math.ceil(length / itemsPerPage);

  // Tableau pour la boucle for avec map ( des nombre de pages de la pagination)
  const pages = [];
  //   console.log(pages);

  for (let index = 1; index < pageCount; index++) {
    pages.push(index);
  }
  return (
    <div>
      <ul className="pagination pagination-sm">
        <li className={`page-item ${currentPage === 1 && "disabled"}`}>
          <button
            className="page-link"
            onClick={() => {
              onPageChanged(currentPage - 1);
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
                onPageChanged(page);
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
              onPageChanged(currentPage + 1);
            }}
          >
            &raquo;
          </button>
        </li>
      </ul>
    </div>
  );
};
// Ajouter une fonction à l'objet Pagination
Pagination.getData = (items, currentPage, itemsPerPage) => {
  // Calculer le start

  // D'ou on part(start ) et combien ( itemsPerPage )
  const start = currentPage * itemsPerPage - itemsPerPage;

  return items.slice(start, start + itemsPerPage);
};
export default Pagination;
