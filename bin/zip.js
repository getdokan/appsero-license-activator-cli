import fs from 'fs-extra';
import { exec } from 'child_process' ;

import chalk from 'chalk' ;

const pluginFiles = [
    'src/',
    'vendor/',
    'LICENSE',
    'appsero-license-activator-cli.php',
    'composer.json',
];

const removeFiles = [ 'composer.json', 'composer.lock' ];

const { version } = JSON.parse( fs.readFileSync( 'package.json' ) );

exec(
    'rm -rf *',
    {
        cwd: 'build',
    },
    ( error ) => {
        if ( error ) {
            console.log(
                chalk.yellow(
                    `⚠️ Could not find the build directory.`
                )
            );
            console.log(
                chalk.green(
                    `🗂 Creating the build directory ...`
                )
            );
            // Making build folder.
            fs.mkdirp( 'build' );
        }

        const dest = 'build/appsero-license-activator-cli'; // Temporary folder name after coping all the files here.
        fs.mkdirp( dest );

        console.log( `🗜 Started making the zip ...` );
        try {
            console.log( `⚙️ Copying plugin files ...` );

            // Coping all the files into build folder.
            pluginFiles.forEach( ( file ) => {
                fs.copySync( file, `${ dest }/${ file }` );
            } );
            console.log( `📂 Finished copying files.` );
        } catch ( err ) {
            console.error( chalk.red( '❌ Could not copy plugin files.' ), err );
            return;
        }

        exec(
            'composer install && composer install --optimize-autoloader --no-dev',
            {
                cwd: dest
            },
            ( error ) => {
                if ( error ) {
                    console.log(
                        chalk.red(
                            `❌ Could not install composer in ${ dest } directory.`
                        )
                    );
                    console.log( chalk.bgRed.black( error ) );

                    return;
                }

                console.log(
                    `⚡️ Installed composer packages in ${ dest } directory.`
                );

                // Removing files that is not needed in the production now.
                removeFiles.forEach( ( file ) => {
                    fs.removeSync( `${ dest }/${ file }` );
                } );

                // Output zip file name.
                const zipFile = `appsero-license-activator-cli-v${ version }.zip`;

                console.log( `📦 Making the zip file ${ zipFile } ...` );

                // Making the zip file here.
                exec(
                    `zip ${ zipFile } appsero-license-activator-cli -rq`,
                    {
                        cwd: 'build'
                    },
                    ( error ) => {
                        if ( error ) {
                            console.log(
                                chalk.red( `❌ Could not make ${ zipFile }.` )
                            );
                            console.log( chalk.bgRed.black( error ) );

                            return;
                        }

                        fs.removeSync( dest );
                        console.log(
                            chalk.green( `✅  ${ zipFile } is ready. 🎉` )
                        );
                    }
                );
            }
        );
    }
);