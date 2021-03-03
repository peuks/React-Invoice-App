import React, { useEffect, useState } from "react";
// Permet de faire des requêtes http
import axios from "axios";

const CustomersPage = (props) => {
  /**
   * Le state se définit par customers avec sa méthode
   * setCustomer qui permet de modifier la variable customers
   */
  const [customers, setCustomers] = useState([]);

  // currentPage est un stage dont la page par défaut est définit à 1
  const [currentPage, setCurrentPage] = useState(1);

  /**
   * Créatioin de l'effet, le deuxième paramètre [] contient la variable à surveiller
   * pour lancer une effect à chaque fois qu'une variable change.
   * On ne surveille pas de variable . On donne une variable vide.
   * La fonction se lancera qu'une seule fois juste quand le composant va s'afficher
   */

  useEffect(() => {
    axios
      .get("https://localhost:8000/api/customers")
      .then((response) => response.data["hydra:member"])

      // Une fois que l'on récupère le tableau data ,
      // changer ce qu'il y a  dans le state customer ave ce que ce l'on récupère ,

      .then((data) => setCustomers(data))
      // Récupération de l'erreur si elle existe
      .catch((error) => console.log(error.response));
  }, []);

  const handleDelete = (id) => {
    // Copie du tableau des customers
    const originalCustomers = [...customers];

    // Supprimer visuellement
    setCustomers(customers.filter((customer) => customer.id !== id));

    axios
      .delete(`https://localhost:8000/api/customers/${id}`)
      // Lorsque la suppression est faite alors supprimer de
      .then((response) => console.log("ok"))
      .catch((error) => {
        // Remettre la liste des customers avant suppression si celle ci a échouée
        setCustomers(originalCustomers);
        console.log(error.response);
      });
  };

  const handleCangePage = (page) => {
    setCurrentPage(page);
  };
  // Pagination
  const itemsPerPage = 10;

  // Arrondir à l'entier supérieur
  const pageCount = Math.ceil(customers.length / itemsPerPage);

  // Tableau pour la boucle for avec map ( des nombre de pages de la pagination)
  const pages = [];
  console.log(pages);

  for (let index = 1; index < pageCount; index++) {
    pages.push(index);
  }

  // D'ou on part(start ) et combien ( itemsPerPage )
  const start = currentPage * itemsPerPage - itemsPerPage;

  const paginatedCustomers = customers.slice(start, start + itemsPerPage);
  return (
    <>
      <h1>Liste des clients</h1>
      <table className="table table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Email</th>
            <th>Entreprise</th>
            <th className="text-center">Factures</th>
            <th className="text-center">Montant total</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          {paginatedCustomers.map((customer) => (
            <tr key={customer.id}>
              <td>{customer.id}</td>
              <td>
                <a href="#">
                  {customer.firstName} {customer.lastName}
                </a>
              </td>
              <td>{customer.email}</td>
              <td>{customer.compagny}</td>
              <td className="text-center">
                {customer.totalAmount.toLocaleString()} €
              </td>
              <td>
                <button
                  // Si on clique sur le bouton, lancer la function fléchée handleDelete avec comme param customer.id
                  onClick={() => {
                    handleDelete(customer.id);
                  }}
                  // Désactiver le bouton si l'on a des factures ( on ne veut pas d'orphelin )
                  disabled={customer.invoices.length > 0}
                  className="btn btn-sm btn-danger"
                >
                  Supprimer
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

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
          <li
            className={`page-item ${currentPage === pageCount && "disabled"}`}
          >
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
    </>
  );
};

export default CustomersPage;
