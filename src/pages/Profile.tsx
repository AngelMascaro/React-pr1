import axios from 'axios'
import React, { useState, useEffect } from 'react'
import '../App.css'
import { useParams } from 'react-router-dom';

interface User{
    Username:string
    Email:string
}

function Profile() {

    useEffect(() => {
        // getData();

      }, []);

    const userId = useParams();
    // const [userData, setUserData] = useState<User({})

    // const getData = () =>{
    //     axios.get("http://localhost:80/pr1/php/api/usuaris/perfil/" + userId.id)
    //     .then((r)=>  setUserData(r.data))
    //     console.log(userData)

    // }

    return (
        <div className='profile'>
            {/* <p>{userData?.Email}</p> */}

        </div>
    )
}

export default Profile

