import { Routes, Route } from "react-router-dom"
import Home from "./pages/Home"
import About from "./pages/About"
import Signup from "./pages/Signup"
import Profile from "./pages/Profile"
import Login from "./pages/Login"
import { Link } from "react-router-dom";
import Logo from './logo.svg';
import { useState } from "react"
import axios from "axios"
import Users from "./pages/Users"

function App() {

  const [logged, setLogged] = useState(false)
  const [userId, setUserId] = useState("")
  const [userUsername, setUserUsername] = useState("")

  const login = (id:string, username:string)=>{
    setLogged(true)
    setUserId(id)
    setUserUsername(username)
  }
  const logout = ()=>{
    setLogged(false)
    setUserId("")
    setUserUsername("")
  }



  return (

    <div className="App">
      

      <nav className="navbar navbar-expand-md navbar-dark">
      
      <div className="mx-3 mr-5 d-md-none d-sm-block" >
        <img src={Logo} alt="" width="50px"/>
      </div>

      <button className="navbar-toggler mx-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
        <span className="navbar-toggler-icon"></span>
      </button>

      <div className="collapse navbar-collapse" id="navbarScroll">
      <div className=" logonav mx-3  d-none d-md-block" >
            <img src={Logo} alt="" width="50px"/>
          </div>
        <ul className="navbar-nav mx-auto">

          <li className="nav-item mx-3 my-sm-4 my-1">
            <Link to="/">HOME</Link>
          </li>
          <li className="nav-item mx-3 my-sm-4 my-1">
            <Link to="/Users">USERS</Link>
          </li>

          {
            logged === false ?
            <li className="nav-item mx-3 my-sm-4 my-1">
              <Link to="/Signup">SIGNUP</Link>
            </li>
            :
            <p></p>
          }

          {
            logged === false ?
            <li className="nav-item mx-3 my-sm-4 my-1">
              <Link to="/Login">LOGIN</Link>
            </li>
            :
            <p></p>
          }

          {
            logged===true ?
            <li className="nav-item mx-3 my-sm-4 my-1">
              <Link to={`/Profile/${userId}`}>PROFILE: {userUsername}</Link>
            </li>
            :
            <li className="nav-item mx-3 my-sm-4 my-1">
              <Link to="/Profile">{userUsername}</Link>
            </li>        
          }
          {
            logged===true ?
            <li className="nav-item mx-3 my-sm-4 my-1">
              <Link to={"/"} onClick={logout}>LOGOUT</Link>
            </li>
            :
            <p></p>      
          }

        </ul>
      </div>
    </nav>
      <Routes>
        <Route path="/" element={<Home/>} />
        <Route path="About" element={<About/>} />
        <Route path="Signup"  element={<Signup logged={login} />} />
        <Route path="Login"  element={<Login logged={login} />} />
        <Route path="Profile/:id" element={<Profile userLoggedId={userId}/>} />
        <Route path="Users" element={<Users/>} />
      </Routes>
    </div>
  )
}

export default App