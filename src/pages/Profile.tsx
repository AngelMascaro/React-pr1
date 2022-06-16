import axios from 'axios'
import React, { useState, useEffect } from 'react'
import '../App.css'
import { useParams } from 'react-router-dom';
import UserData from '../components/UserData';


interface User{
    Username:""
    Email:""
    Birthday:""
}

interface Props{
    userLoggedId:string
}

function Profile(props:Props) {

    const userId = useParams();
    const [userData, setUserData] = useState<User>();

    useEffect(() => {
        getData();
      },[userId]);

    const getData = () =>{
        axios.get("http://localhost:80/pr1/php/api/usuaris/perfil/" + userId.id)
        .then((r)=>  setUserData(r.data[0]))
        .catch(error => console.log(error))
        console.log(userData)
    }

    


    return (
        <div className='profile'>
            <br />
            <UserData
                Username={userData?.Username} 
                Email={userData?.Email}
                Src={`http://localhost:80/pr1/php/api/images/fotos_perfil_upload/${userId.id}.jpg`}
                Birthday={userData?.Birthday}
            />
            {
                props.userLoggedId.toString() === userId.id.toString() ? 
                <h1>hola</h1>
                :
                <h1>no</h1>
            }

        </div>
    )
}

export default Profile

