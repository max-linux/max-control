
  README.txt proporcionado por max-control para netlogon


  1.- Prevenir cambios

       Si quiere preservar sus archivos de login y que las 
       actualizaciones no los sobreescriban, 
       cree un archivo vacío de nombre ".lock"


                sudo touch /home/samba/netlogon/.touch



  2.- Políticas


     2.1.- Para Windows XP se usa ntconfig.pol (se edita con poledit)

     2.2.- Para Windows Vista y superior se usan scripts kixtart.

           Windows Vista y superior usan un sistema de permisos 
           en el registro que hay que configurar para que los usuarios
           del dominio puedan escribir.

           Proceso:

            * Entrar en el sistema como administrador.
            * Unir el equipo al dominio (no reiniciar aún).
            * Inicio -> Ejecutar -> cmd (y escribir)

                net use z: \\max-server\netlogon

            * Abrir Mi PC (o Equipo) -> Z:
            * Botón derecho sobre winvista.win7.win2008.registry.fix.bat -> Ejecutar como Administrador

            * Reiniciar

            * En el primer arranque entrar con un usuario (Administrador) 
              del dominio y comprobar que

                    ·  el fondo de pantalla, 
                    ·  la ocultación de unidades y 
                    ·  la página de inicio

              son correctas.

          






