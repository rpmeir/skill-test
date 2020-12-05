#!/bin/sh

apt-get update -yqq && apt-get install -yqq \
    apt-utils \
    curl \
    dnsutils \
    gdb \
    git \
    htop \
    iproute2 \
    iputils-ping \
    ltrace \
    make \
    procps \
    strace \
    sudo \
    sysstat \
    unzip \
    vim \
    wget \
    libnss3-tools \
;
