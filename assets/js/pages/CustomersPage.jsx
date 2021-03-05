import React, { useEffect, useState } from "react";
// Permet de faire des requêtes http
import axios from "axios";
import Pagination from "../components/Pagination";

const CustomersPage = (props) => {
  /**
   * Le state se définit par customers avec sa méthode
   * setCustomer qui permet de modifier la variable customers
   */
  const [customers, setCustomers] = useState([]);

  // currentPage est un stage dont la page par défaut est définit à 1
  const [currentPage, setCurrentPage] = useState(1);

  const [search, setSearch] = useState("");

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

  const handlePageChange = (page) => {
    setCurrentPage(page);
  };

  const handleHandleSearch = (event) => {
    const value = event.currentTarget.value;
  };

  const handleSearch = (event) => {
    const value = event.currentTarget.value;
    setSearch(value);
    setCurrentPage(1);
  };

  const filteredCustomers = customers.filter(
    (c) =>
      c.firstName.toLowerCase().includes(search.toLowerCase()) ||
      c.lastName.toLowerCase().includes(search.toLowerCase()) ||
      c.email.toLowerCase().includes(search.toLowerCase()) ||
      c.company != null && c.company.toLowerCase().includes(search.toLowerCase())
  );

  // Pagination
  const itemsPerPage = 10;

  const paginatedCustomers = Pagination.getData(
    filteredCustomers,

    currentPage,
    // Définit comme une constante un peu plus haut
    itemsPerPage
  );
  return (
    <>
      <h1>Liste des clients</h1>
      <div className="form-group">
        <input
          onChange={handleSearch}
          value={search}
          type="text"
          className="form-control"
          placeholder="Rechercher..."
        />
      </div>

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
              <td>{customer.company}</td>
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
      {itemsPerPage < filteredCustomers.length && (
        <Pagination
          currentPage={currentPage}
          itemsPerPage={itemsPerPage}
          length={filteredCustomers.length}
          onPageChanged={handlePageChange}
        />
      )}
    </>
  );
};

export default CustomersPage;
