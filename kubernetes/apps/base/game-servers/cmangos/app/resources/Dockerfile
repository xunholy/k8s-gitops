FROM ubuntu:24.04 AS builder

ENV DEBIAN_FRONTEND="noninteractive"

ARG TIMEZONE="Etc/UTC"
ENV TZ="${TIMEZONE}"
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
        tzdata \
 && ln -snf "/usr/share/zoneinfo/${TIMEZONE}" /etc/localtime \
 && echo "${TIMEZONE}" > /etc/timezone \
 && dpkg-reconfigure --frontend noninteractive tzdata \
 \
 && apt-get install -y --no-install-recommends \
        build-essential \
        ca-certificates \
        cmake \
        g++-14 \
        git-core \
        libboost-filesystem-dev \
        libboost-program-options-dev \
        libboost-regex-dev \
        libboost-serialization-dev \
        libboost-system-dev \
        libboost-thread-dev \
        libmariadb-dev-compat \
        libssl-dev \
        mariadb-client \
 \
 && update-alternatives --install /usr/bin/gcc gcc \
                                  /usr/bin/gcc-14 14 \
                        --slave /usr/bin/g++ g++ \
        /usr/bin/g++-14 \
 \
 && rm -rf /var/lib/apt/lists/* \
           /tmp/*

ARG MANGOS_SHA1="latest"
ARG DATABASE_SHA1="latest"
ENV HOME_DIR="/home/mangos"
ENV MANGOS_DIR="${HOME_DIR}/mangos"
ENV DATABASE_DIR="${HOME_DIR}/classic-db"

RUN mkdir -p "${MANGOS_DIR}" \
             "${DATABASE_DIR}" \
 \
 && cd /tmp \
 && if [ "${MANGOS_SHA1}" = "latest" ]; \
    then \
        git clone "https://github.com/cmangos/mangos-classic.git" \
                --branch "master" \
                --single-branch \
                --depth 1 \
           "${MANGOS_DIR}"; \
    else \
        git clone "https://github.com/cmangos/mangos-classic.git" \
                --branch "master" \
                --single-branch \
            cmangos-mangos \
     && cd cmangos-mangos \
     && git archive "${MANGOS_SHA1}" | tar xC "${MANGOS_DIR}"; \
    fi \
 \
 && cd /tmp \
 && if [ "${DATABASE_SHA1}" = "latest" ]; \
    then \
        git clone "https://github.com/cmangos/classic-db.git" \
                --branch "master" \
                --single-branch \
                --depth 1 \
            "${DATABASE_DIR}"; \
    else \
        git clone "https://github.com/cmangos/classic-db.git" \
                --branch "master" \
                --single-branch \
            cmangos-db \
     && cd cmangos-db \
     && git archive "${DATABASE_SHA1}" | tar xC "${DATABASE_DIR}"; \
    fi \
 \
 && rm -rf /tmp/*

ARG THREADS="1"
RUN mkdir -p "${HOME_DIR}/build" \
             "${HOME_DIR}/run" \
 \
 && cd "${HOME_DIR}/build" \
 && cmake ../mangos/ \
        -D CMAKE_INSTALL_PREFIX=../run \
        -D DEBUG=0 \
        -D PCH=1 \
        -D BUILD_AHBOT=ON \
        -D BUILD_EXTRACTORS=ON \
        -D BUILD_METRICS=ON \
        -D BUILD_PLAYERBOTS=ON \
        -D BUILD_SCRIPTDEV=ON \
 \
 && make -j "${THREADS}" \
 && make install \
 \
 && cd "${HOME_DIR}/run/bin/tools" \
 && chmod +x ExtractResources.sh \
             MoveMapGen.sh

RUN useradd --comment "MaNGOS" \
            --home "${HOME_DIR}" \
            --user-group mangos

WORKDIR "${HOME_DIR}"

ENV MYSQL_SUPERUSER="root"
ENV MYSQL_SUPERPASS=""

ENV MANGOS_DBHOST="host.docker.internal"
ENV MANGOS_DBPORT="3306"
ENV MANGOS_DBUSER="mangos"
ENV MANGOS_DBPASS=""

ENV MANGOS_WORLD_DBNAME="classicmangos"
ENV MANGOS_CHARACTERS_DBNAME="classiccharacters"
ENV MANGOS_LOGS_DBNAME="classiclogs"
ENV MANGOS_REALMD_DBNAME="classicrealmd"

COPY builder/entrypoint.sh /
COPY builder/InstallFullDB.config "${DATABASE_DIR}/"

ENTRYPOINT ["/entrypoint.sh"]
CMD ["bash"]

ENV VOLUME_DIR="/home/mangos/data"
ENV TMPDIR="${VOLUME_DIR}/tmp"
VOLUME ["${VOLUME_DIR}"]

ARG COMMIT_SHA
ARG CREATE_DATE
ARG VERSION
LABEL org.opencontainers.image.title="CMaNGOS Classic Builder"
LABEL org.opencontainers.image.description="CMaNGOS Classic builder image with DB tools, extractors, and full world DB."
LABEL org.opencontainers.image.licenses="GPL-2.0"
LABEL org.opencontainers.image.version="${VERSION}"
LABEL org.opencontainers.image.revision="${COMMIT_SHA}"
LABEL org.opencontainers.image.source="https://github.com/xunholy/k8s-gitops"
LABEL org.opencontainers.image.url="https://github.com/xunholy/k8s-gitops"
LABEL org.opencontainers.image.created="${CREATE_DATE}"

LABEL "net.cmangos.mangos-classic.revision"="${MANGOS_SHA1}"
LABEL "net.cmangos.mangos-classic.source"="https://github.com/cmangos/mangos-classic"
LABEL "net.cmangos.classic-db.revision"="${DATABASE_SHA1}"
LABEL "net.cmangos.classic-db.source"="https://github.com/cmangos/classic-db"

FROM ubuntu:24.04 AS runner

ENV DEBIAN_FRONTEND="noninteractive"

ARG TIMEZONE="Etc/UTC"
ENV TZ="${TIMEZONE}"
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
        tzdata \
 && ln -snf "/usr/share/zoneinfo/${TIMEZONE}" /etc/localtime \
 && echo "${TIMEZONE}" > /etc/timezone \
 && dpkg-reconfigure --frontend noninteractive tzdata \
 \
 && apt-get install -y --no-install-recommends \
        gosu \
        libmariadb-dev \
        libssl3 \
        wait-for-it \
 \
 && rm -rf /var/lib/apt/lists/* \
           /tmp/*

ENV HOME_DIR="/home/mangos"
ENV MANGOS_DIR="/opt/mangos"
RUN useradd --home "${HOME_DIR}" --create-home \
            --comment "MaNGOS" \
            --user-group mangos

WORKDIR "${MANGOS_DIR}"

COPY --from=builder /home/mangos/run "${MANGOS_DIR}"
COPY runner/entrypoint.sh /

ENV VOLUME_DIR="/var/lib/mangos"
ENV TMPDIR="${VOLUME_DIR}/tmp"
RUN mkdir "${VOLUME_DIR}" \
 && sed -i '/^DataDir/c\DataDir = "'"${VOLUME_DIR}"'"' etc/mangosd.conf.dist

ENV MANGOS_DBHOST="host.docker.internal"
ENV MANGOS_DBPORT="3306"
ENV MANGOS_DBUSER="mangos"
ENV MANGOS_DBPASS=""

ENV MANGOS_WORLD_DBNAME="classicmangos"
ENV MANGOS_CHARACTERS_DBNAME="classiccharacters"
ENV MANGOS_LOGS_DBNAME="classiclogs"
ENV MANGOS_REALMD_DBNAME="classicrealmd"

ENTRYPOINT ["/entrypoint.sh"]
CMD ["bash"]

EXPOSE 3724 7878 8085
VOLUME ["${VOLUME_DIR}"]

ARG COMMIT_SHA
ARG CREATE_DATE
ARG VERSION
LABEL org.opencontainers.image.title="CMaNGOS Classic Runner"
LABEL org.opencontainers.image.description="CMaNGOS Classic runner image with mangosd/realmd and playerbot support."
LABEL org.opencontainers.image.licenses="GPL-2.0"
LABEL org.opencontainers.image.version="${VERSION}"
LABEL org.opencontainers.image.revision="${COMMIT_SHA}"
LABEL org.opencontainers.image.source="https://github.com/xunholy/k8s-gitops"
LABEL org.opencontainers.image.url="https://github.com/xunholy/k8s-gitops"
LABEL org.opencontainers.image.created="${CREATE_DATE}"

ARG MANGOS_SHA1
LABEL "net.cmangos.mangos-classic.revision"="${MANGOS_SHA1}"
LABEL "net.cmangos.mangos-classic.source"="https://github.com/cmangos/mangos-classic"

ARG DATABASE_SHA1
LABEL "net.cmangos.classic-db.revision"="${DATABASE_SHA1}"
LABEL "net.cmangos.classic-db.source"="https://github.com/cmangos/classic-db"
