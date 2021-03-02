import React, { useEffect, useState } from "react";
// Permet de faire des requêtes http
import axios from "axios";

const CustomersPage = (props) => {
  /**
   * Le state se définit par customers avec sa méthode
   * setCustomer qui permet de modifier la variable customers
   */
  const [customers, setCustomers] = useState([]);

  /**
   * Créatioin de l'effet, le deuxième paramètre [] contient la variable à surveiller
   * pour lancer une ffect à chaque fois qu'une variable change.
   * On ne surveille pas de variable . On donne une variable vide.
   * La fonction se lancera qu'une seule fois juste quand le composant va s'afficher
   */

  useEffect(() => {
    axios
      .get("https://localhost:8000/api/customers")
      .then((response) => response.data["hydra:member"])

      // Une fois que l'on récupère le tableau data ,
      // changer ce qu'il y a  dans le state customer ave ce que ce l'on récupère ,

      .then((data) => setCustomers(data));
  }, []);

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
          {/* customers est un tableau que l'on mappe en retourant tout les tr */}
          {customers.map((customer) => {
            console.log("test");
            <h1>test</h1>;
            // Pour l'optimiser rajouter une clée avec une id
          })}
        </tbody>
      </table>
    </>
  );
};

export default CustomersPage;
