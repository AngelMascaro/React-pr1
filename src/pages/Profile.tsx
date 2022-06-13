import axios from 'axios'
import React, { useState, useEffect } from 'react'
import '../App.css'
import { useParams } from 'react-router-dom';

interface User{
    Username:""
    Email:""
}

function Profile() {

    
    const userId = useParams();
    const [userData, setUserData] = useState<User>();

    useEffect(() => {
        getData();
      }, []);

    const getData = () =>{
        axios.get("http://localhost:80/pr1/php/api/usuaris/perfil/" + userId.id)
        .then((r)=>  setUserData(r.data[0]))
        .catch(error => console.log(error))
        console.log(userData)

    }

    return (
        <div className='profile'>
            <p>{userData?.Username}</p>
            <p>{userData?.Email}</p>

        </div>
    )
}

export default Profile

