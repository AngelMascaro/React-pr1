import { Routes, Route } from "react-router-dom"
import Home from "./pages/Home"
import About from "./pages/About"
import Signup from "./pages/Signup"
import Profile from "./pages/Profile"
import { Link } from "react-router-dom";
// import { Navbar } from "react-bootstrap";
import Logo from './logo.svg';

function App() {
  return (

    <div className="App">
      

      <nav className="navbar navbar-expand-md navbar-dark">
      
      <div className="mr-5 d-md-none d-sm-block" >
        <img src={Logo} alt="" width="70px"/>
      </div>

      <button className="navbar-toggler mx-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
        <span className="navbar-toggler-icon"></span>
      </button>

      <div className="collapse navbar-collapse" id="navbarScroll">

        <ul className="navbar-nav mx-auto">

          <li className="nav-item mx-3 my-sm-4 my-1">
            <Link to="/">HOME</Link>
          </li>

          <li className="nav-item mx-3 my-sm-4 my-1">
            <Link to="/Signup">SIGNUP</Link>
          </li>

          <div className="mx-5  d-none d-md-block" >
            <img src={Logo} alt="" width="100px"/>
          </div>

          <li className="nav-item mx-3 my-sm-4 my-1">
            <Link to="/About">ABOUT</Link>
          </li>

          <li className="nav-item mx-3 my-sm-4 my-1">
            <Link to="/About">ABOUT</Link>
          </li>

        </ul>
      </div>
    </nav>

      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="About" element={<About />} />
        <Route path="Signup" element={<Signup />} />
        <Route path="Profile/:id" element={<Profile />} />
      </Routes>
      
    </div>
  )
}

export default App