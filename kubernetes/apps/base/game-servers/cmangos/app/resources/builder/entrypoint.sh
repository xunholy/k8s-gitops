#!/usr/bin/env bash
#

readonly SCRIPT_VERSION="1.0.0"

set -e

# Utils:
#
function echoerr()
{
    echo ${@} >&2
}

function success()
{
    echo -e "\e[32m${1}\e[0m"
}
function info()
{
    echo -e "\e[36m${1}\e[0m"
}
function warning()
{
    if [[ "${2}" == "--underline" ]]
    then
        echo -e "\e[4;33m${1}\e[0m"
    else
        echo -e "\e[33m${1}\e[0m"
    fi
}
function error()
{
    if [[ "${2}" == "--underline" ]]
    then
        echo -e "\e[4;31m${1}\e[0m"
    else
        echo -e "\e[31m${1}\e[0m"
    fi
}

function mysql_execute()
{
    mariadb "-h${MANGOS_DBHOST}" "-P${MANGOS_DBPORT}" "-u${MYSQL_SUPERUSER}" "-p${MYSQL_SUPERPASS}" ${@}
}
function mysql_dump()
{
    local DATABASE_NAME="${1}"
    local OUTPUT_FILE="${2}"

    mariadb-dump "-h${MANGOS_DBHOST}" "-P${MANGOS_DBPORT}" "-u${MYSQL_SUPERUSER}" "-p${MYSQL_SUPERPASS}" \
        "${DATABASE_NAME}" --opt --result-file="${OUTPUT_FILE}"
}

# Sub-functions:
#
function install_updates()
{
    cd "${DATABASE_DIR}"

    ./InstallFullDB.sh -UpdateCore

    if [[ "${1}" == "--world" ]]
    then
        ./InstallFullDB.sh -World
    fi
}

# Main functions:
#
function extract_resources_from_client()
{
    cd "${VOLUME_DIR}"

    if [[ -f ".resext" ]] || [[ $(ls | grep -c -E "Cameras|dbc|maps|mmaps|vmaps") -gt 0 ]]
    then
        echo ""
        echo -e " $(warning "WARNING!" --underline)"
        echo -e "  $(warning "└") It seems that you've already extracted the resources from the client before."
        echo -e "    If you continue, existing resources will be overwritten by the new ones."
        echo ""
        read -p "Are you sure to continue? [Y/n]: " ANSWER

        if [[ "${ANSWER}" != "y" ]] && [[ "${ANSWER}" != "Y" ]]
        then
            echo -e " └ Ok, no problem! Resources have been left untouched."

            return
        fi

        rm -rf Cameras/ \
               dbc/ \
               maps/ \
               mmaps/ \
               vmaps/
    fi

    cd "${HOME_DIR}/run/bin/tools"

    cp * "${HOME_DIR}/wow-client/"
    cd "${HOME_DIR}/wow-client"

    ./ExtractResources.sh ${@}

    mv Cameras "${VOLUME_DIR}/Cameras"
    mv dbc "${VOLUME_DIR}/dbc"
    mv maps "${VOLUME_DIR}/maps"
    mv mmaps "${VOLUME_DIR}/mmaps"
    mv vmaps "${VOLUME_DIR}/vmaps"

    mkdir -p "${VOLUME_DIR}/logs"
    mv *.log "${VOLUME_DIR}/logs/"

    rm -rf Buildings/ \
       \
       ExtractResources.sh \
       MoveMapGen \
       MoveMapGen.sh \
       ad \
       offmesh.txt \
       vmap_assembler \
       vmap_extractor

    echo "${SCRIPT_VERSION}" > "${VOLUME_DIR}/.resext"
}
function init_db()
{
    cd "${DATABASE_DIR}"

    echo ""
    echo "This procedure will create all the databases required by the server"
    echo " to run properly and will initialize them with the default data."
    echo ""
    echo -e " $(warning "WARNING!" --underline)"
    echo -e "  $(warning "└") Please note that, if you have already initialized the databases before,"
    echo -e "     this procedure will prune $(info "ALL") of your data and"
    echo -e "     they will be lost $(info "FOREVER") (it's a very long time)!"
    echo ""
    read -p "Are you sure to continue? [Y/n]: " ANSWER

    if [[ "${ANSWER}" != "y" ]] && [[ "${ANSWER}" != "Y" ]]
    then
        echo -e " └ Ok, no problem! Databases have been left untouched."

        return
    fi

    echo -e " └ Please, wait... Initializing databases..."
    echo ""
    echo -e " --------------------------------------"

    ./InstallFullDB.sh -InstallAll "${MYSQL_SUPERUSER}" "${MYSQL_SUPERPASS}" DeleteAll
}
function backup_db()
{
    readonly HELP_MSG="
Backups the specified database(s) and then returns the
 result as a single \"tar.gz\" file via standard output.

Usage:
    backup-db [OPTIONS...]

Options:
    -a | --all
        Backups all databases.

    -w | --world
        Backups the world database: \"$(info "${MANGOS_WORLD_DBNAME}")\".

    -c | --characters
        Backups the characters database: \"$(info "${MANGOS_CHARACTERS_DBNAME}")\".

    -l | --logs
        Backups the logs database: \"$(info "${MANGOS_LOGS_DBNAME}")\".

    -r | --realmd
        Backups the realmd database: \"$(info "${MANGOS_REALMD_DBNAME}")\".

    -h | -? | --help
        Displays this help message.
"

    declare -A DATABASES

    while [[ ${#} -gt 0 ]]
    do
        case "${1}" in
            -a | --all)
                readonly BACKUPS_ALL="true"
                ;;
            -w | --world)
                DATABASES+=(["world"]="${MANGOS_WORLD_DBNAME}")
                ;;
            -c | --characters)
                DATABASES+=(["characters"]="${MANGOS_CHARACTERS_DBNAME}")
                ;;
            -l | --logs)
                DATABASES+=(["logs"]="${MANGOS_LOGS_DBNAME}")
                ;;
            -r | --realmd)
                DATABASES+=(["realmd"]="${MANGOS_REALMD_DBNAME}")
                ;;
            -h | -? | --help)
                echo -e "${HELP_MSG}"

                exit 0
                ;;
            *)
                echoerr ""
                echoerr -e " $(error "ERROR!" --underline)"
                echoerr -e "  $(error "└") Unknown option: \"$(info "${1}")\""
                echoerr ""
                echoerr " Run \"$(info "backup-db --help")\" for more information."

                exit 1
                ;;
        esac

        shift
    done

    if [[ "${BACKUPS_ALL}" == "true" ]]
    then
        if [[ -n ${DATABASES[@]} ]]
        then
            echoerr ""
            echoerr -e " $(error "ERROR!" --underline)"
            echoerr -e "  $(error "└") You cannot specify both \"$(info "--all")\" and any other"
            echoerr -e "     specific database options at the same time."
            echoerr ""
            echoerr " Run \"$(info "backup-db --help")\" for more information."

            exit 2
        fi

        DATABASES=(["world"]="${MANGOS_WORLD_DBNAME}" \
                   ["characters"]="${MANGOS_CHARACTERS_DBNAME}" \
                   ["logs"]="${MANGOS_LOGS_DBNAME}" \
                   ["realmd"]="${MANGOS_REALMD_DBNAME}")
    fi
    if [[ -z ${DATABASES[@]} ]]
    then
        echoerr ""
        echoerr -e " $(error "ERROR!" --underline)"
        echoerr -e "  $(error "└") You must specify at least one database to backup."
        echoerr ""
        echoerr " Run \"$(info "backup-db --help")\" for more information."

        exit 3
    fi

    local TIMESTAMP="$(date +"%Y-%m-%d_%H-%M-%S")"
    local BACKUP_DIR="/home/mangos/data/backups/${TIMESTAMP}"
    local BACKUP_FILE="${BACKUP_DIR}/backup_${TIMESTAMP}.tar.gz"

    mkdir -p "${BACKUP_DIR}"

    for DATABASE in ${!DATABASES[@]}
    do
        local DATABASE_NAME="${DATABASES["${DATABASE}"]}"
        local OUTPUT_FILENAME="${DATABASE}.sql"

        mysql_dump "${DATABASE_NAME}" "${BACKUP_DIR}/${OUTPUT_FILENAME}"
    done

    cd "${BACKUP_DIR}"

    echo "${SCRIPT_VERSION}" > .version
    tar -czvf "${BACKUP_FILE}" .version $(ls *.sql | xargs -n 1) > /dev/null

    cat "${BACKUP_FILE}"
}
function manage_db()
{
    cd "${DATABASE_DIR}"

    ./InstallFullDB.sh
}
function restore_db()
{
    local TIMESTAMP="$(date +"%Y-%m-%d_%H-%M-%S")"
    local TEMP_DIR="${TMPDIR}/${TIMESTAMP}"
    local BACKUP_FILE="${TEMP_DIR}/backup_${TIMESTAMP}.tar.gz"

    mkdir -p "${TEMP_DIR}"
    cat - > "${BACKUP_FILE}"

    cd "${TEMP_DIR}"

    tar -xzvf "${BACKUP_FILE}" -C . > /dev/null

    local BACKUP_FILES=($(ls *.sql | xargs -n 1))

    for BACKUP_FILE in ${BACKUP_FILES[@]}
    do
        local DATABASE="${BACKUP_FILE%.sql}"

        if [[ "${DATABASE}" == "world" ]]
        then
            local DATABASE_NAME="${MANGOS_WORLD_DBNAME}"
        elif [[ "${DATABASE}" == "characters" ]]
        then
            local DATABASE_NAME="${MANGOS_CHARACTERS_DBNAME}"
        elif [[ "${DATABASE}" == "logs" ]]
        then
            local DATABASE_NAME="${MANGOS_LOGS_DBNAME}"
        elif [[ "${DATABASE}" == "realmd" ]]
        then
            local DATABASE_NAME="${MANGOS_REALMD_DBNAME}"
        fi

        mysql_execute "${DATABASE_NAME}" < "${TEMP_DIR}/${BACKUP_FILE}"
    done
}
function update_db()
{
    readonly HELP_MSG="
Updates databases by applying the latest changes
 to the database structure and default data.

Usage:
    update-db [OPTIONS...]

Options:
    -w | --world
        Updates the world database: \"$(info "${MANGOS_WORLD_DBNAME}")\".

    -h | -? | --help
        Displays this help message.
"
    if [[ -n "${1}" ]]
    then
        case "${1}" in
            -w | --world)
                echo ""
                echo -e " $(warning "WARNING!" --underline)"
                echo -e "  $(warning "└") This procedure will prune all custom data you"
                echo -e "     may have loaded into your \"$(info "${MANGOS_WORLD_DBNAME}")\" database."
                echo ""
                read -p "Are you sure to continue? [Y/n]: " ANSWER

                if [[ "${ANSWER}" != "y" ]] && [[ "${ANSWER}" != "Y" ]]
                then
                    echo -e " └ Ok, no problem! Database have been left untouched."

                    return
                fi

                echo -e " └ Please, wait... Updating database..."
                echo ""
                echo -e " --------------------------------------"

                install_updates --world

                ;;
            -h | -? | --help)
                echo -e "${HELP_MSG}"

                exit 0
                ;;
            *)
                echoerr ""
                echoerr -e " $(error "ERROR!" --underline)"
                echoerr -e "  $(error "└") Unknown option: \"$(info "${1}")\""
                echoerr ""
                echoerr " Run \"$(info "update-db --help")\" for more information."

                exit 1
                ;;
        esac
    else
        install_updates
    fi
}

# Execution:
#
case "${1}" in
    extract)
        shift

        extract_resources_from_client ${@}
        ;;
    init-db)
        shift

        init_db
        ;;
    backup-db)
        shift

        backup_db ${@}
        ;;
    restore-db)
        shift

        restore_db ${@}
        ;;
    manage-db)
        shift

        manage_db
        ;;
    update-db)
        shift

        update_db ${@}
        ;;
    *)
        cd "${HOME_DIR}"

        exec ${@}
        ;;
esac
