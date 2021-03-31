import React, { useEffect, useState } from "react";
// Permet de faire des requêtes http
import axios from "axios";
import Pagination from "../components/Pagination";

const CustomersPageWithPagination = (props) => {
  /**
   * Le state se définit par customers avec sa méthode
   * setCustomer qui permet de modifier la variable customers
   */
  const [customers, setCustomers] = useState([]);
  const [totalItems, setTotalItems] = useState(0);
  // currentPage est un stage dont la page par défaut est définit à 1
  const [currentPage, setCurrentPage] = useState(1);
  const [loading, setLoading] = useState(true);

  // Pagination
  const itemsPerPage = 10;
  /**
   * Créatioin de l'effet, le deuxième paramètre [] contient la variable à surveiller
   * pour lancer une effect à chaque fois qu'une variable change.
   * Dans notre cas la fonction useEffect s'execute à chaque fois que la variable
   * currentPage change
   */

  useEffect(() => {
    axios
      // Activer la pagination dans la requête
      .get(
        `https://localhost:8001/api/customers?pagination=true&count=${itemsPerPage}&page=${currentPage}`
      )
      .then((response) => {
        setCustomers(response.data["hydra:member"]);
        setTotalItems(response.data["hydra:totalItems"]);
        setLoading(false);
      })

      // Récupération de l'erreur si elle existe
      .catch((error) => console.log(error.response));
  }, [currentPage]);

  const handleDelete = (id) => {
    // Copie du tableau des customers
    const originalCustomers = [...customers];

    // Supprimer visuellement
    setCustomers(customers.filter((customer) => customer.id !== id));

    axios
      .delete(`https://localhost:8001/api/customers/${id}`)
      // Lorsque la suppression est faite alors supprimer de
      //   .then((response) => console.log("ok"))
      .catch((error) => {
        // Remettre la liste des customers avant suppression si celle ci a échouée
        setCustomers(originalCustomers);
        console.log(error.response);
      });
  };

  const handlePageChange = (page) => {
    setCurrentPage(page);
    setLoading(true);
  };

  const paginatedCustomers = Pagination.getData(
    customers,
    currentPage,
    // Définit comme une constante un peu plus haut
    itemsPerPage
  );

  return (
    <>
      <h1>Liste des clients (Pagination)</h1>
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
          {/* Si loading true alors afficher le chargement */}
          {loading && (
            <tr>
              <td>Chargement en cours</td>
            </tr>
          )}
          {!loading &&
            customers.map((customer) => (
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
      <Pagination
        currentPage={currentPage}
        itemsPerPage={itemsPerPage}
        length={totalItems}
        onPageChanged={handlePageChange}
      />
    </>
  );
};

export default CustomersPageWithPagination;
