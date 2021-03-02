/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

//  Import React

import React from "react";
import ReactDom from "react-dom";
import { HashRouter, Switch, Route } from "react-router-dom";

// any CSS you import will output into a single css file (app.css in this case)
import "../styles/app.scss";

// start the Stimulus application
import "../bootstrap";
import Navbar from "./components/NavBar";
import HomePage from "./pages/HomePage";
import CustomersPage from "./pages/CustomersPage";

// Module <App />
const App = () => {
  return (
    <HashRouter>
      <Navbar />
      <main className="container pt-5">
        <Switch>
          <Route path="/customers" component={CustomersPage} />
          <Route path="/" component={HomePage} />
        </Switch>
      </main>
    </HashRouter>
  );
};

// Définir notre root Element
const rootElement = document.querySelector("#app");

// Demander à react de générer le composant < APP /> dans rootElement

ReactDom.render(<App />, rootElement);
