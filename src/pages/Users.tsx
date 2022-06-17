// import axios from 'axios'
import axios from 'axios'
import { useEffect, useState } from 'react'
import '../App.css'
import { Link } from "react-router-dom";

interface Users{
    Username:string
    Email:string
    Birthday:string
    User_id:string
}

function Users() {

    const [users, setUsers] = useState(Array<Users>())

    const getData = () =>{
        axios.get("http://localhost:80/pr1/php/api/usuaris/allUsers/")
        .then((r)=>  {

            console.log("data",r.data);
            setUsers(r.data);
        })
        .catch(error => console.log(error))
        console.log("users",users)
    }
    useEffect(() => {
        getData();
      }, []);

    return (
        <div className='users'>
            <div className='container-users-users'>
            {   
                users.length !== 0 ?
                users.map((user:Users, index)=>

                <div key={index} className='user'>
                <Link to={`/Profile/${user.User_id}`}>
                    <div className='users-users'>
                        <img className='my-2' height="80px" src={`http://localhost:80/pr1/php/api/images/fotos_perfil_upload/${user.User_id}.jpg`} />
                        <div>{user.Username}</div>
                        {/* <div>{user.Email}</div>
                        <div>{user.Birthday}</div> */}
                        <br />
                    </div>
                </Link>

                </div>
                
                )
                :
                <p></p>
        }

            </div>
        </div>
    )
}

export default Users
